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
        'nilai' => 'required|decimal|greater_than_equal_to[0]',
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
            // Pastikan tipe diisi jika kosong
            if (empty($item['tipe'])) {
                $item['tipe'] = 'element_to_element';
            }
            if ($this->save($item)) {
                $saved++;
            }
        }

        return $saved;
    }

    /**
     * Build supermatrix from interdependensi data
     */
    public function buildSupermatrix($subkriteria, $interdependensi, $clusters)
    {
        $n = count($subkriteria);
        $supermatrix = array_fill(0, $n, array_fill(0, $n, 0.0));

        // Map subkriteria id to index (karena subkriteria adalah node/element dalam ANP)
        $indexMap = [];
        foreach ($subkriteria as $index => $sk) {
            $indexMap[$sk['id']] = $index;
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
     * Calculate eigenvalues using power method (optimized)
     */
    private function calculateEigenvalues($matrix)
    {
        $n = count($matrix);
        if ($n == 0) return [];
        
        // Cache untuk matriks yang sama (simple caching)
        static $cache = [];
        $cacheKey = md5(serialize($matrix));
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        // Power method to find dominant eigenvalue (optimized)
        $maxIterations = 50; // Reduced from 100
        $tolerance = 0.00001; // More precise
        
        // Start with uniform vector (optimized initialization)
        $vector = array_fill(0, $n, 1.0 / $n);
        $eigenvalue = 0;
        
        for ($iter = 0; $iter < $maxIterations; $iter++) {
            // Multiply matrix by vector (optimized)
            $newVector = $this->matrixVectorMultiply($matrix, $vector);
            
            // Calculate eigenvalue using Rayleigh quotient (optimized)
            $numerator = $this->dotProduct($newVector, $vector);
            $denominator = $this->dotProduct($vector, $vector);
            
            if ($denominator > 0) {
                $newEigenvalue = $numerator / $denominator;
            } else {
                $newEigenvalue = 0;
            }
            
            // Normalize new vector (optimized)
            $norm = sqrt($this->dotProduct($newVector, $newVector));
            if ($norm > 0) {
                $scale = 1.0 / $norm;
                for ($i = 0; $i < $n; $i++) {
                    $newVector[$i] *= $scale;
                }
            }
            
            // Check convergence
            $diff = 0;
            $eigenvalueDiff = abs($newEigenvalue - $eigenvalue);
            for ($i = 0; $i < $n; $i++) {
                $diff += abs($newVector[$i] - $vector[$i]);
            }
            
            $vector = $newVector;
            $eigenvalue = $newEigenvalue;
            
            // Check both vector and eigenvalue convergence
            if ($diff < $tolerance && $eigenvalueDiff < $tolerance) {
                $result = [$eigenvalue];
                $cache[$cacheKey] = $result; // Cache the result
                return $result;
            }
        }
        
        // If not converged, use trace/n as approximation (optimized)
        $trace = 0;
        for ($i = 0; $i < $n; $i++) {
            $trace += $matrix[$i][$i];
        }
        $result = [$trace / $n];
        $cache[$cacheKey] = $result; // Cache the result
        return $result;
    }
    
    /**
     * Optimized matrix-vector multiplication
     */
    private function matrixVectorMultiply($matrix, $vector)
    {
        $n = count($matrix);
        $result = array_fill(0, $n, 0.0);
        
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            $row = $matrix[$i];
            for ($j = 0; $j < $n; $j++) {
                $sum += $row[$j] * $vector[$j];
            }
            $result[$i] = $sum;
        }
        
        return $result;
    }
    
    /**
     * Optimized dot product
     */
    private function dotProduct($a, $b)
    {
        $n = count($a);
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $a[$i] * $b[$i];
        }
        return $sum;
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
    public function extractWeights($limitSupermatrix, $subkriteria)
    {
        $n = count($limitSupermatrix);
        if ($n == 0) return [];
        
        $weights = [];
        
        // In ANP, weights are typically taken from the limit supermatrix
        // We can take the average of each row (or column) as the weight
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0;
            for ($j = 0; $j < $n; $j++) {
                $rowSum += $limitSupermatrix[$i][$j];
            }
            $weights[$i] = $rowSum / $n; // Average of row
        }
        
        // Normalize weights to sum to 1
        $total = array_sum($weights);
        if ($total > 0) {
            foreach ($weights as &$weight) {
                $weight /= $total;
            }
        }
        
        // Map weights to subkriteria (NOT kriteria)
        $result = [];
        foreach ($subkriteria as $index => $sk) {
            $result[] = [
                'subkriteria_id' => $sk['id'],
                'kriteria_id' => $sk['kriteria_id'],
                'kode' => $sk['kode'],
                'nama' => $sk['nama'],
                'kriteria_nama' => $sk['kriteria_nama'],
                'weight' => isset($weights[$index]) ? $weights[$index] : 0
            ];
        }
        
        return $result;
    }

    /**
     * Get histori pairwise comparison
     */
    public function getHistoriPairwise($periodeId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('anp_pairwise_histori');
        
        if ($periodeId) {
            $builder->where('periode_id', $periodeId);
        }
        
        $builder->orderBy('created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Build interdependensi matrix from histori pairwise data
     */
    public function bangunMatriksDariHistori($periodeId = null)
    {
        log_message('debug', '=== MULAI bangunMatriksDariHistori ===');
        log_message('debug', 'Periode ID: ' . $periodeId);

        // Ambil data histori pairwise
        $historiData = $this->getHistoriPairwise($periodeId);
        log_message('debug', 'Jumlah data histori: ' . count($historiData));

        if (empty($historiData)) {
            log_message('debug', 'Tidak ada data histori pairwise ditemukan');
            return null;
        }

        // Ambil subkriteria untuk mapping
        $subkriteriaModel = new \App\Models\SubkriteriaModel();
        $subkriteria = $subkriteriaModel->findAll();
        log_message('debug', 'Jumlah subkriteria: ' . count($subkriteria));

        // Buat mapping dari subkriteria_id ke index matriks
        $nodeIds = [];
        foreach ($subkriteria as $sk) {
            $nodeIds[] = $sk['id'];
        }
        log_message('debug', 'Node IDs: ' . json_encode($nodeIds));

        $n = count($nodeIds);
        $matriks = array_fill(0, $n, array_fill(0, $n, 0.0));

        // Isi matriks dengan data histori
        $dataCount = 0;
        foreach ($historiData as $row) {
            $indexDari = array_search($row['node_dari_id'], $nodeIds);
            $indexKe = array_search($row['node_ke_id'], $nodeIds);

            log_message('debug', 'Processing: node_dari_id=' . $row['node_dari_id'] . ' -> indexDari=' . $indexDari);
            log_message('debug', 'Processing: node_ke_id=' . $row['node_ke_id'] . ' -> indexKe=' . $indexKe);

            if ($indexDari !== false && $indexKe !== false) {
                $nilai = (float)$row['skala'];
                $matriks[$indexDari][$indexKe] = $nilai;
                $dataCount++;
                log_message('debug', 'Set matriks[' . $indexDari . '][' . $indexKe . '] = ' . $nilai);
            } else {
                log_message('debug', 'Mapping gagal untuk node_dari_id=' . $row['node_dari_id'] . ' atau node_ke_id=' . $row['node_ke_id']);
            }
        }

        log_message('debug', 'Jumlah data yang berhasil dimasukkan: ' . $dataCount);

        // Set diagonal ke 1 (self-comparison)
        for ($i = 0; $i < $n; $i++) {
            $matriks[$i][$i] = 1.0;
        }

        log_message('debug', '=== SELESAI bangunMatriksDariHistori ===');
        return $matriks;
    }
}
