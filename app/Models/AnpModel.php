<?php

namespace App\Models;

use CodeIgniter\Model;

class AnpModel extends Model
{
    protected $table = 'anp_interdependensi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cluster_id_dari',
        'cluster_id_ke',
        'kriteria_id_dari',
        'kriteria_id_ke',
        'nilai',
        'tipe',
        'periode_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'cluster_id_dari' => 'required|integer',
        'cluster_id_ke' => 'required|integer',
        'nilai' => 'required|decimal|greater_than[0]',
        'tipe' => 'required|in_list[cluster_to_cluster,element_to_element,cluster_to_element,element_to_cluster]',
        'periode_id' => 'permit_empty|integer'
    ];
    
    protected $validationMessages = [
        'nilai' => [
            'required' => 'Nilai interdependensi harus diisi',
            'decimal' => 'Nilai harus berupa angka desimal',
            'greater_than' => 'Nilai harus lebih besar dari 0'
        ],
        'tipe' => [
            'required' => 'Tipe interdependensi harus dipilih',
            'in_list' => 'Tipe interdependensi tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get interdependensi by periode
     */
    public function getByPeriode($periodeId)
    {
        return $this->where('periode_id', $periodeId)->findAll();
    }

    /**
     * Get interdependensi element to element
     */
    public function getElementInterdependensi($periodeId = null)
    {
        $builder = $this->where('tipe', 'element_to_element');
        if ($periodeId) {
            $builder->where('periode_id', $periodeId);
        }
        return $builder->findAll();
    }

    /**
     * Get interdependensi cluster to cluster
     */
    public function getClusterInterdependensi($periodeId = null)
    {
        $builder = $this->where('tipe', 'cluster_to_cluster');
        if ($periodeId) {
            $builder->where('periode_id', $periodeId);
        }
        return $builder->findAll();
    }

    /**
     * Save interdependensi matrix
     */
    public function saveMatrix($data, $periodeId = null)
    {
        // Delete existing data for this periode
        if ($periodeId) {
            $this->where('periode_id', $periodeId)->delete();
        }

        // Save new data
        $saved = 0;
        foreach ($data as $item) {
            $item['periode_id'] = $periodeId;
            if ($this->save($item)) {
                $saved++;
            }
        }

        return $saved;
    }

    /**
     * Build supermatrix from interdependensi data
     */
    public function buildSupermatrix($kriteria, $interdependensi, $clusters)
    {
        $n = count($kriteria);
        $supermatrix = array_fill(0, $n, array_fill(0, $n, 0.0));

        // Map kriteria id to index
        $indexMap = [];
        foreach ($kriteria as $index => $k) {
            $indexMap[$k['id']] = $index;
        }

        // Fill supermatrix with interdependensi values
        foreach ($interdependensi as $item) {
            if ($item['tipe'] === 'element_to_element' && 
                isset($indexMap[$item['kriteria_id_dari']]) && 
                isset($indexMap[$item['kriteria_id_ke']])) {
                
                $i = $indexMap[$item['kriteria_id_dari']];
                $j = $indexMap[$item['kriteria_id_ke']];
                $supermatrix[$i][$j] = (float)$item['nilai'];
            }
        }

        // Set diagonal to 1 for self-comparison
        for ($i = 0; $i < $n; $i++) {
            $supermatrix[$i][$i] = 1.0;
        }

        return $supermatrix;
    }

    /**
     * Calculate consistency ratio for matrix
     */
    public function calculateConsistency($matrix)
    {
        $n = count($matrix);
        if ($n <= 1) {
            return ['ci' => 0, 'cr' => 0, 'konsisten' => true];
        }

        // Calculate eigenvalues
        $eigenvalues = $this->calculateEigenvalues($matrix);
        $lambdaMax = max($eigenvalues);

        // Consistency Index (CI)
        $ci = ($lambdaMax - $n) / ($n - 1);

        // Random Index (RI) - Saaty's values
        $riTable = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
        $ri = isset($riTable[$n]) ? $riTable[$n] : 1.49;

        // Consistency Ratio (CR)
        $cr = ($ri > 0) ? $ci / $ri : 0;

        return [
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'ri' => $ri,
            'cr' => $cr,
            'konsisten' => $cr < 0.1
        ];
    }

    /**
     * Calculate eigenvalues using power method (simplified)
     */
    private function calculateEigenvalues($matrix)
    {
        $n = count($matrix);
        $eigenvalues = [];

        // Simplified calculation - for ANP we use different approach
        // This is a placeholder for actual eigenvalue calculation
        for ($i = 0; $i < $n; $i++) {
            $rowSum = array_sum($matrix[$i]);
            $eigenvalues[] = $rowSum / $n;
        }

        return $eigenvalues;
    }

    /**
     * Normalize supermatrix
     */
    public function normalizeSupermatrix($supermatrix)
    {
        $n = count($supermatrix);
        $normalized = array_fill(0, $n, array_fill(0, $n, 0.0));

        // Normalize by column sum
        for ($j = 0; $j < $n; $j++) {
            $colSum = 0;
            for ($i = 0; $i < $n; $i++) {
                $colSum += $supermatrix[$i][$j];
            }

            if ($colSum > 0) {
                for ($i = 0; $i < $n; $i++) {
                    $normalized[$i][$j] = $supermatrix[$i][$j] / $colSum;
                }
            }
        }

        return $normalized;
    }

    /**
     * Calculate limit supermatrix (convergence)
     */
    public function calculateLimitSupermatrix($weightedSupermatrix, $maxIterations = 100, $tolerance = 0.0001)
    {
        $n = count($weightedSupermatrix);
        $current = $weightedSupermatrix;
        
        for ($iter = 0; $iter < $maxIterations; $iter++) {
            $next = $this->matrixMultiply($current, $current);
            
            // Check for convergence
            $diff = 0;
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $diff += abs($next[$i][$j] - $current[$i][$j]);
                }
            }
            
            if ($diff < $tolerance) {
                return $next;
            }
            
            $current = $next;
        }
        
        return $current;
    }

    /**
     * Matrix multiplication
     */
    private function matrixMultiply($a, $b)
    {
        $n = count($a);
        $result = array_fill(0, $n, array_fill(0, $n, 0.0));
        
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $sum = 0;
                for ($k = 0; $k < $n; $k++) {
                    $sum += $a[$i][$k] * $b[$k][$j];
                }
                $result[$i][$j] = $sum;
            }
        }
        
        return $result;
    }

    /**
     * Extract weights from limit supermatrix
     */
    public function extractWeights($limitSupermatrix, $kriteria)
    {
        $n = count($limitSupermatrix);
        $weights = [];
        
        // Take the first column as weights (simplified approach)
        // In proper ANP, we take the column corresponding to the goal
        for ($i = 0; $i < $n; $i++) {
            $weights[$i] = $limitSupermatrix[$i][0];
        }
        
        // Normalize weights to sum to 1
        $total = array_sum($weights);
        if ($total > 0) {
            foreach ($weights as &$weight) {
                $weight /= $total;
            }
        }
        
        // Map weights to kriteria
        $result = [];
        foreach ($kriteria as $index => $k) {
            $result[] = [
                'kriteria_id' => $k['id'],
                'kode' => $k['kode'],
                'nama' => $k['nama'],
                'weight' => isset($weights[$index]) ? $weights[$index] : 0
            ];
        }
        
        return $result;
    }
}
