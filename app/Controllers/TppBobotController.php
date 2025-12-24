<?php

namespace App\Controllers;

use App\Models\KriteriaModel;

class TppBobotController extends BaseController
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
        
        // Hitung total bobot
        $totalBobot = $this->kriteriaModel->getTotalBobot();
        
        $data = [
            'title' => 'Input Bobot Kriteria - SPK Pembinaan',
            'kriteria' => $kriteria,
            'totalBobot' => $totalBobot,
            'activeMenu' => 'input-bobot'
        ];
        
        return view('tpp_bobot/index', $data);
    }

    public function simpan()
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'bobot.*' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[1]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Ambil data bobot dari form
        $bobotData = $this->request->getPost('bobot');
        $kriteriaIds = $this->request->getPost('kriteria_id');
        
        // Update bobot untuk setiap kriteria
        foreach ($kriteriaIds as $index => $id) {
            $data = [
                'bobot' => floatval($bobotData[$index])
            ];
            $this->kriteriaModel->update($id, $data);
        }
        
        // Hitung total bobot baru
        $totalBobot = $this->kriteriaModel->getTotalBobot();
        
        // Simpan total bobot ke session untuk validasi
        session()->setFlashdata('totalBobot', $totalBobot);
        
        return redirect()->to('/tpp/bobot')->with('success', 'Bobot kriteria berhasil diperbarui! Total bobot: ' . number_format($totalBobot, 3));
    }

    public function matriksPerbandingan()
    {
        // Ambil semua kriteria
        $kriteria = $this->kriteriaModel->findAll();
        
        // Inisialisasi matriks perbandingan berpasangan
        $matriks = [];
        $n = count($kriteria);
        
        // Buat matriks identitas awal
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i == $j) {
                    $matriks[$i][$j] = 1; // Diagonal utama = 1
                } else {
                    $matriks[$i][$j] = 0; // Belum diisi
                }
            }
        }
        
        $data = [
            'title' => 'Matriks Perbandingan Berpasangan - SPK Pembinaan',
            'kriteria' => $kriteria,
            'matriks' => $matriks,
            'activeMenu' => 'matriks-perbandingan'
        ];
        
        return view('tpp_bobot/matriks', $data);
    }

    public function simpanMatriks()
    {
        // Validasi input matriks
        $validation = \Config\Services::validation();
        $validation->setRules([
            'matriks.*.*' => 'required|numeric|greater_than[0]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Ambil data matriks
        $matriksData = $this->request->getPost('matriks');
        
        // Simpan matriks ke session untuk perhitungan ANP
        session()->set('matriks_perbandingan', $matriksData);
        
        return redirect()->to('/tpp/bobot/konsistensi')->with('success', 'Matriks perbandingan berhasil disimpan!');
    }

    public function konsistensi()
    {
        // Ambil matriks dari session
        $matriks = session()->get('matriks_perbandingan');
        
        if (!$matriks) {
            return redirect()->to('/tpp/bobot/matriks')->with('error', 'Silakan isi matriks perbandingan terlebih dahulu!');
        }
        
        // Validasi struktur matriks
        if (!is_array($matriks) || empty($matriks) || !isset($matriks[0]) || !is_array($matriks[0])) {
            return redirect()->to('/tpp/bobot/matriks')->with('error', 'Matriks perbandingan tidak valid. Silakan isi ulang!');
        }
        
        // Ambil data kriteria untuk ditampilkan
        $kriteria = $this->kriteriaModel->findAll();
        
        // Hitung konsistensi matriks
        $hasilKonsistensi = $this->hitungKonsistensi($matriks);
        
        $data = [
            'title' => 'Validasi Konsistensi Matriks - SPK Pembinaan',
            'matriks' => $matriks,
            'kriteria' => $kriteria,
            'konsistensi' => $hasilKonsistensi,
            'activeMenu' => 'konsistensi'
        ];
        
        return view('tpp_bobot/konsistensi', $data);
    }

    private function hitungKonsistensi($matriks)
    {
        $n = count($matriks);
        
        // Validasi matriks sebelum diproses
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if (!isset($matriks[$i][$j]) || $matriks[$i][$j] === '') {
                    // Set nilai default 1 untuk diagonal, 0 untuk lainnya
                    $matriks[$i][$j] = ($i == $j) ? 1 : 0;
                }
                // Konversi ke float
                $matriks[$i][$j] = floatval($matriks[$i][$j]);
            }
        }
        
        // Hitung jumlah per kolom
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
        
        // Normalisasi matriks
        $matriksNormalisasi = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matriksNormalisasi[$i][$j] = $matriks[$i][$j] / $jumlahKolom[$j];
            }
        }
        
        // Hitung rata-rata per baris (bobot eigen)
        $bobotEigen = [];
        for ($i = 0; $i < $n; $i++) {
            $bobotEigen[$i] = array_sum($matriksNormalisasi[$i]) / $n;
        }
        
        // Hitung λ maksimum
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $jumlahBaris = 0;
            for ($j = 0; $j < $n; $j++) {
                $jumlahBaris += $matriks[$i][$j] * $bobotEigen[$j];
            }
            // Hindari pembagian dengan nol
            if ($bobotEigen[$i] != 0) {
                $lambdaMax += $jumlahBaris / $bobotEigen[$i];
            } else {
                $lambdaMax += 0;
            }
        }
        $lambdaMax = $lambdaMax / $n;
        
        // Hitung Consistency Index (CI)
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0;
        
        // Tabel Random Index (RI) untuk n ≤ 10
        $riTable = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
        $ri = isset($riTable[$n]) ? $riTable[$n] : 1.49;
        
        // Hitung Consistency Ratio (CR)
        $cr = ($ri > 0) ? $ci / $ri : 0;
        
        return [
            'n' => $n,
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'ri' => $ri,
            'cr' => $cr,
            'bobot_eigen' => $bobotEigen,
            'konsisten' => $cr < 0.1,
            'matriks_normalisasi' => $matriksNormalisasi
        ];
    }
}
