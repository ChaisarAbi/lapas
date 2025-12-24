<?php

namespace App\Controllers;

use App\Models\KriteriaModel;

class TppAnpController extends BaseController
{
    protected $kriteriaModel;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
    }

    public function index()
    {
        // Ambil semua kriteria
        $kriteria = $this->kriteriaModel->findAll();
        
        // Ambil matriks perbandingan dari session
        $matriks = session()->get('matriks_perbandingan');
        
        // Jika tidak ada matriks, buat matriks default
        if (!$matriks) {
            $matriks = $this->buatMatriksDefault($kriteria);
        }
        
        // Hitung hasil ANP
        $hasilAnp = $this->hitungANP($matriks);
        
        $data = [
            'title' => 'Hasil Analytic Network Process (ANP) - SPK Pembinaan',
            'kriteria' => $kriteria,
            'matriks' => $matriks,
            'hasilAnp' => $hasilAnp,
            'activeMenu' => 'hasil-anp'
        ];
        
        return view('tpp_anp/index', $data);
    }

    private function buatMatriksDefault($kriteria)
    {
        $n = count($kriteria);
        $matriks = [];
        
        // Buat matriks identitas
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i == $j) {
                    $matriks[$i][$j] = 1;
                } else {
                    // Nilai default: 1 untuk semua (netral)
                    $matriks[$i][$j] = 1;
                }
            }
        }
        
        return $matriks;
    }

    private function hitungANP($matriks)
    {
        // Validasi matriks
        if (!is_array($matriks) || empty($matriks)) {
            // Buat matriks default jika tidak valid
            $kriteria = $this->kriteriaModel->findAll();
            $matriks = $this->buatMatriksDefault($kriteria);
        }
        
        $n = count($matriks);
        
        // Validasi dan normalisasi nilai matriks
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if (!isset($matriks[$i][$j]) || $matriks[$i][$j] === '') {
                    $matriks[$i][$j] = ($i == $j) ? 1 : 1; // Default 1 untuk ANP
                }
                $matriks[$i][$j] = floatval($matriks[$i][$j]);
            }
        }
        
        // 1. Hitung jumlah per kolom
        $jumlahKolom = [];
        for ($j = 0; $j < $n; $j++) {
            $jumlahKolom[$j] = 0;
            for ($i = 0; $i < $n; $i++) {
                $jumlahKolom[$j] += $matriks[$i][$j];
            }
            // Hindari pembagian dengan nol
            if ($jumlahKolom[$j] == 0) {
                $jumlahKolom[$j] = 1;
            }
        }
        
        // 2. Normalisasi matriks
        $matriksNormalisasi = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matriksNormalisasi[$i][$j] = $matriks[$i][$j] / $jumlahKolom[$j];
            }
        }
        
        // 3. Hitung rata-rata per baris (bobot eigen/prioritas)
        $bobotPrioritas = [];
        for ($i = 0; $i < $n; $i++) {
            $bobotPrioritas[$i] = array_sum($matriksNormalisasi[$i]) / $n;
        }
        
        // 4. Hitung λ maksimum
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $jumlahBaris = 0;
            for ($j = 0; $j < $n; $j++) {
                $jumlahBaris += $matriks[$i][$j] * $bobotPrioritas[$j];
            }
            // Hindari pembagian dengan nol
            if ($bobotPrioritas[$i] != 0) {
                $lambdaMax += $jumlahBaris / $bobotPrioritas[$i];
            } else {
                $lambdaMax += 0;
            }
        }
        $lambdaMax = $lambdaMax / $n;
        
        // 5. Hitung Consistency Index (CI)
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0;
        
        // 6. Tabel Random Index (RI)
        $riTable = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
        $ri = isset($riTable[$n]) ? $riTable[$n] : 1.49;
        
        // 7. Hitung Consistency Ratio (CR)
        $cr = ($ri > 0) ? $ci / $ri : 0;
        
        // 8. Hitung bobot akhir (normalisasi bobot prioritas)
        $totalBobotPrioritas = array_sum($bobotPrioritas);
        $bobotAkhir = [];
        foreach ($bobotPrioritas as $bobot) {
            $bobotAkhir[] = ($totalBobotPrioritas > 0) ? $bobot / $totalBobotPrioritas : 0;
        }
        
        return [
            'n' => $n,
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'ri' => $ri,
            'cr' => $cr,
            'konsisten' => $cr < 0.1,
            'bobot_prioritas' => $bobotPrioritas,
            'bobot_akhir' => $bobotAkhir,
            'matriks_normalisasi' => $matriksNormalisasi,
            'jumlah_kolom' => $jumlahKolom
        ];
    }

    public function simpanBobotAkhir()
    {
        // Ambil bobot akhir dari POST
        $bobotAkhir = $this->request->getPost('bobot_akhir');
        $kriteriaIds = $this->request->getPost('kriteria_id');
        
        // Validasi
        $validation = \Config\Services::validation();
        $validation->setRules([
            'bobot_akhir.*' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[1]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Update bobot di database
        foreach ($kriteriaIds as $index => $id) {
            $data = [
                'bobot' => floatval($bobotAkhir[$index])
            ];
            $this->kriteriaModel->update($id, $data);
        }
        
        return redirect()->to('/tpp/anp')->with('success', 'Bobot akhir ANP berhasil disimpan ke database!');
    }

    public function cetakLaporan()
    {
        // Ambil semua kriteria
        $kriteria = $this->kriteriaModel->findAll();
        
        // Ambil matriks perbandingan dari session
        $matriks = session()->get('matriks_perbandingan');
        
        // Hitung hasil ANP
        $hasilAnp = $this->hitungANP($matriks);
        
        $data = [
            'title' => 'Laporan Hasil ANP - SPK Pembinaan',
            'kriteria' => $kriteria,
            'matriks' => $matriks,
            'hasilAnp' => $hasilAnp,
            'tanggal' => date('d/m/Y H:i:s')
        ];
        
        return view('tpp_anp/cetak', $data);
    }
}
