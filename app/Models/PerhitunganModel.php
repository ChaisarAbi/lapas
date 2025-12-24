<?php

namespace App\Models;

use CodeIgniter\Model;

class PerhitunganModel extends Model
{
    protected $table = '';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Hitung ranking menggunakan metode TOPSIS
     */
    public function hitungRankingTOPSIS($narapidana, $kriteria, $penilaian)
    {
        $hasil = [];
        
        // 1. Buat matriks keputusan
        $matriks = [];
        foreach ($narapidana as $napi) {
            $row = [];
            foreach ($kriteria as $k) {
                $nilai = 0;
                foreach ($penilaian as $p) {
                    if ($p['narapidana_id'] == $napi['id'] && $p['kriteria_id'] == $k['id']) {
                        $nilai = (float)$p['nilai'];
                        break;
                    }
                }
                $row[] = $nilai;
            }
            $matriks[] = $row;
        }
        
        // 2. Normalisasi matriks
        $normalisasi = [];
        $jumlahKolom = count($kriteria);
        
        for ($j = 0; $j < $jumlahKolom; $j++) {
            $sumSquares = 0;
            for ($i = 0; $i < count($matriks); $i++) {
                $sumSquares += pow($matriks[$i][$j], 2);
            }
            $sqrtSum = sqrt($sumSquares);
            
            for ($i = 0; $i < count($matriks); $i++) {
                $normalisasi[$i][$j] = $sqrtSum > 0 ? $matriks[$i][$j] / $sqrtSum : 0;
            }
        }
        
        // 3. Matriks terbobot
        $terbobot = [];
        for ($i = 0; $i < count($normalisasi); $i++) {
            for ($j = 0; $j < $jumlahKolom; $j++) {
                $terbobot[$i][$j] = $normalisasi[$i][$j] * (float)$kriteria[$j]['bobot'];
            }
        }
        
        // 4. Solusi ideal positif dan negatif
        // Asumsi semua kriteria adalah benefit (semakin tinggi nilai semakin baik)
        $idealPositif = [];
        $idealNegatif = [];
        
        for ($j = 0; $j < $jumlahKolom; $j++) {
            $kolom = array_column($terbobot, $j);
            $idealPositif[$j] = max($kolom);
            $idealNegatif[$j] = min($kolom);
        }
        
        // 5. Hitung jarak ke solusi ideal
        for ($i = 0; $i < count($terbobot); $i++) {
            $dPositif = 0;
            $dNegatif = 0;
            
            for ($j = 0; $j < $jumlahKolom; $j++) {
                $dPositif += pow($terbobot[$i][$j] - $idealPositif[$j], 2);
                $dNegatif += pow($terbobot[$i][$j] - $idealNegatif[$j], 2);
            }
            
            $dPositif = sqrt($dPositif);
            $dNegatif = sqrt($dNegatif);
            
            // 6. Hitung nilai preferensi
            $preferensi = ($dPositif + $dNegatif) > 0 ? $dNegatif / ($dPositif + $dNegatif) : 0;
            
            $hasil[$i] = [
                'narapidana' => $narapidana[$i],
                'd_positif' => $dPositif,
                'd_negatif' => $dNegatif,
                'preferensi' => $preferensi
            ];
        }
        
        // 7. Urutkan berdasarkan preferensi (descending)
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
        return $hasil;
    }

    /**
     * Hitung konsistensi ANP
     */
    public function hitungKonsistensiANP($matriksPerbandingan)
    {
        // Implementasi perhitungan konsistensi ANP
        // Ini adalah contoh sederhana
        $n = count($matriksPerbandingan);
        $totalKolom = array_fill(0, $n, 0);
        
        // Hitung total per kolom
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $totalKolom[$j] += $matriksPerbandingan[$i][$j];
            }
        }
        
        // Normalisasi matriks
        $matriksNormalisasi = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matriksNormalisasi[$i][$j] = $matriksPerbandingan[$i][$j] / $totalKolom[$j];
            }
        }
        
        // Hitung vektor prioritas
        $vektorPrioritas = [];
        for ($i = 0; $i < $n; $i++) {
            $vektorPrioritas[$i] = array_sum($matriksNormalisasi[$i]) / $n;
        }
        
        // Hitung lambda max
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $matriksPerbandingan[$i][$j] * $vektorPrioritas[$j];
            }
            $lambdaMax += $sum / $vektorPrioritas[$i];
        }
        $lambdaMax = $lambdaMax / $n;
        
        // Hitung Consistency Index (CI)
        $ci = ($lambdaMax - $n) / ($n - 1);
        
        // Hitung Consistency Ratio (CR)
        $ri = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
        $cr = $ci / ($ri[$n] ?? 1.49);
        
        return [
            'vektor_prioritas' => $vektorPrioritas,
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'cr' => $cr,
            'konsisten' => $cr < 0.1
        ];
    }
}
