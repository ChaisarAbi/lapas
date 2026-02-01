<?php

namespace App\Models;

use CodeIgniter\Model;

class TopsisModel extends Model
{
    protected $table = 'ranking';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'periode_id',
        'narapidana_id',
        'nilai_preferensi',
        'ranking',
        'status',
        'detail_perhitungan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'periode_id' => 'required|integer',
        'narapidana_id' => 'required|integer',
        'nilai_preferensi' => 'required|decimal',
        'ranking' => 'required|integer',
        'status' => 'required|in_list[Remisi Penuh,Remisi Separuh,Tidak Layak]'
    ];
    
    protected $validationMessages = [
        'periode_id' => [
            'required' => 'Periode harus dipilih',
            'integer' => 'ID periode harus berupa angka'
        ],
        'narapidana_id' => [
            'required' => 'Narapidana harus dipilih',
            'integer' => 'ID narapidana harus berupa angka'
        ],
        'nilai_preferensi' => [
            'required' => 'Nilai preferensi harus diisi',
            'decimal' => 'Nilai preferensi harus berupa angka desimal'
        ],
        'ranking' => [
            'required' => 'Ranking harus diisi',
            'integer' => 'Ranking harus berupa angka bulat'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus salah satu dari: Remisi Penuh, Remisi Separuh, Tidak Layak'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Hitung TOPSIS untuk periode tertentu
     */
    public function hitungTopsis($periode)
    {
        // Ambil data penilaian untuk periode dalam format matriks keputusan
        $penilaianModel = new PenilaianModel();
        $matriksData = $penilaianModel->getMatriksKeputusan($periode);
        
        if (empty($matriksData['matriks']) || empty($matriksData['narapidana_list'])) {
            return ['error' => 'Tidak ada data penilaian untuk periode ini'];
        }
        
        // Ambil bobot dari subkriteria (bobot global)
        $bobot = $this->getBobotSubkriteria();
        
        if (empty($bobot)) {
            return ['error' => 'Bobot subkriteria belum tersedia. Silakan hitung ANP terlebih dahulu.'];
        }
        
        // Struktur data untuk perhitungan
        $hasil = $this->prosesTopsisSubkriteria($matriksData, $bobot);
        
        return $hasil;
    }
    
    /**
     * Ambil bobot dari subkriteria (bobot global)
     */
    private function getBobotSubkriteria()
    {
        $db = \Config\Database::connect();
        
        // Ambil bobot dari subkriteria yang sudah dihitung ANP
        $builder = $db->table('subkriteria s');
        $builder->select('s.id, s.kode, s.nama, s.bobot, s.jenis');
        $builder->where('s.bobot >', 0);
        $builder->orderBy('s.kode', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        return $result;
    }
    
    /**
     * Proses perhitungan TOPSIS dengan data subkriteria
     */
    private function prosesTopsisSubkriteria($matriksData, $bobot)
    {
        $matriks = $matriksData['matriks'];
        $narapidanaList = $matriksData['narapidana_list'];
        $subkriteriaList = $matriksData['subkriteria_list'];
        
        // 1. Buat matriks keputusan dalam format array 2D
        $matriksKeputusan = [];
        $narapidanaInfo = [];
        
        foreach ($narapidanaList as $index => $narapidanaId) {
            $row = [];
            foreach ($subkriteriaList as $subkriteriaId) {
                if (isset($matriks[$narapidanaId][$subkriteriaId])) {
                    $row[] = $matriks[$narapidanaId][$subkriteriaId]['nilai'];
                    // Simpan info narapidana
                    if (!isset($narapidanaInfo[$narapidanaId])) {
                        $narapidanaInfo[$narapidanaId] = [
                            'nama' => $matriks[$narapidanaId][$subkriteriaId]['narapidana_nama'],
                            'subkriteria_kode' => $matriks[$narapidanaId][$subkriteriaId]['subkriteria_kode']
                        ];
                    }
                } else {
                    $row[] = 0; // Nilai default jika tidak ada data
                }
            }
            $matriksKeputusan[] = $row;
        }
        
        // 2. Urutkan bobot sesuai dengan urutan subkriteria
        $bobotTerurut = [];
        foreach ($subkriteriaList as $subkriteriaId) {
            $found = false;
            foreach ($bobot as $b) {
                if ($b['id'] == $subkriteriaId) {
                    $bobotTerurut[] = $b;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                // Jika bobot tidak ditemukan, gunakan default
                $bobotTerurut[] = [
                    'id' => $subkriteriaId,
                    'kode' => 'SUB' . $subkriteriaId,
                    'nama' => 'Subkriteria ' . $subkriteriaId,
                    'bobot' => 0.1,
                    'jenis' => 'Benefit'
                ];
            }
        }
        
        // 3. Normalisasi matriks
        $matriksNormalisasi = $this->normalisasiMatriks($matriksKeputusan);
        
        // 4. Matriks terbobot
        $matriksTerbobot = $this->matriksTerbobot($matriksNormalisasi, $bobotTerurut);
        
        // 5. Solusi ideal positif dan negatif
        $solusiIdeal = $this->hitungSolusiIdeal($matriksTerbobot, $bobotTerurut);
        
        // 6. Jarak ke solusi ideal
        $jarak = $this->hitungJarak($matriksTerbobot, $solusiIdeal);
        
        // 7. Nilai preferensi (Ci)
        $nilaiPreferensi = $this->hitungNilaiPreferensi($jarak);
        
        // 8. Ranking
        $ranking = $this->hitungRanking($nilaiPreferensi);
        
        // 9. Status berdasarkan nilai preferensi
        $status = $this->tentukanStatus($nilaiPreferensi);
        
        // 10. Siapkan hasil akhir
        $hasil = [];
        foreach ($narapidanaList as $index => $narapidanaId) {
            // Cari nama narapidana dari data matriks
            $namaNarapidana = '';
            $nomorRegistrasi = '';
            
            // Ambil dari data pertama yang ada
            foreach ($subkriteriaList as $subkriteriaId) {
                if (isset($matriks[$narapidanaId][$subkriteriaId])) {
                    $namaNarapidana = $matriks[$narapidanaId][$subkriteriaId]['narapidana_nama'];
                    // Untuk nomor registrasi, kita perlu query database
                    break;
                }
            }
            
            // Jika tidak ditemukan, query database
            if (empty($namaNarapidana)) {
                $db = \Config\Database::connect();
                $builder = $db->table('narapidana');
                $builder->select('nama_lengkap, nomor_registrasi');
                $builder->where('id', $narapidanaId);
                $narapidanaData = $builder->get()->getRowArray();
                
                if ($narapidanaData) {
                    $namaNarapidana = $narapidanaData['nama_lengkap'];
                    $nomorRegistrasi = $narapidanaData['nomor_registrasi'];
                }
            }
            
            $hasil[] = [
                'narapidana_id' => $narapidanaId,
                'nama' => $namaNarapidana,
                'nomor_registrasi' => $nomorRegistrasi,
                'nilai_preferensi' => $nilaiPreferensi[$index],
                'ranking' => $ranking[$index],
                'status' => $status[$index],
                'jarak_positif' => $jarak['positif'][$index],
                'jarak_negatif' => $jarak['negatif'][$index]
            ];
        }
        
        // 11. Detail perhitungan untuk disimpan
        $detailPerhitungan = [
            'matriks_keputusan' => $matriksKeputusan,
            'matriks_normalisasi' => $matriksNormalisasi,
            'matriks_terbobot' => $matriksTerbobot,
            'solusi_ideal' => $solusiIdeal,
            'bobot' => $bobotTerurut,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return [
            'hasil' => $hasil,
            'detail' => $detailPerhitungan,
            'total_narapidana' => count($narapidanaList),
            'total_subkriteria' => count($subkriteriaList)
        ];
    }
    
    /**
     * Ambil bobot dari ANP (subkriteria bobot global)
     */
    private function getBobotAnp()
    {
        $db = \Config\Database::connect();
        
        // Ambil bobot dari subkriteria yang sudah dihitung ANP
        $builder = $db->table('subkriteria s');
        $builder->select('s.id, s.kode, s.nama, s.bobot, k.jenis');
        $builder->join('kriteria k', 'k.id = s.kriteria_id');
        $builder->where('s.bobot >', 0);
        $builder->orderBy('s.kode', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        // Jika tidak ada bobot di subkriteria, ambil dari kriteria
        if (empty($result)) {
            $builder = $db->table('kriteria');
            $builder->select('id, kode, nama, bobot, jenis');
            $builder->where('bobot >', 0);
            $builder->orderBy('kode', 'ASC');
            
            $result = $builder->get()->getResultArray();
        }
        
        return $result;
    }
    
    /**
     * Proses perhitungan TOPSIS dengan mapping kriteria ke subkriteria
     */
    private function prosesTopsis($penilaian, $bobot)
    {
        // 1. Group penilaian by narapidana
        $narapidanaData = [];
        foreach ($penilaian as $item) {
            $narapidanaId = $item['narapidana_id'];
            if (!isset($narapidanaData[$narapidanaId])) {
                $narapidanaData[$narapidanaId] = [
                    'id' => $narapidanaId,
                    'nama' => $item['nama_lengkap'],
                    'nomor_registrasi' => $item['nomor_registrasi'],
                    'nilai_kriteria' => []
                ];
            }
            $narapidanaData[$narapidanaId]['nilai_kriteria'][$item['kriteria_id']] = $item['nilai_konversi'];
        }
        
        // 2. Mapping kriteria ke subkriteria
        // Ambil mapping kriteria-subkriteria dari database
        $db = \Config\Database::connect();
        $builder = $db->table('subkriteria s');
        $builder->select('s.id as subkriteria_id, s.kriteria_id, s.kode as subkriteria_kode, s.bobot, k.kode as kriteria_kode');
        $builder->join('kriteria k', 'k.id = s.kriteria_id');
        $builder->orderBy('s.kode', 'ASC');
        $subkriteriaList = $builder->get()->getResultArray();
        
        // 3. Buat matriks keputusan untuk subkriteria
        $matriksKeputusan = [];
        $narapidanaIds = array_keys($narapidanaData);
        
        foreach ($narapidanaIds as $nId) {
            $row = [];
            foreach ($subkriteriaList as $subkriteria) {
                $kriteriaId = $subkriteria['kriteria_id'];
                // Gunakan nilai kriteria untuk semua subkriteria yang terkait
                $nilai = $narapidanaData[$nId]['nilai_kriteria'][$kriteriaId] ?? 0;
                $row[] = $nilai;
            }
            $matriksKeputusan[] = $row;
        }
        
        // 4. Update bobot dengan data subkriteria yang benar
        $bobotSubkriteria = [];
        foreach ($subkriteriaList as $index => $subkriteria) {
            $bobotSubkriteria[$index] = [
                'id' => $subkriteria['subkriteria_id'],
                'kode' => $subkriteria['subkriteria_kode'],
                'nama' => $subkriteria['subkriteria_kode'], // Gunakan kode sebagai nama
                'bobot' => $subkriteria['bobot'],
                'jenis' => $this->getJenisKriteria($subkriteria['kriteria_id'])
            ];
        }
        
        // Gunakan bobot subkriteria
        $bobot = $bobotSubkriteria;
        
        // 3. Normalisasi matriks
        $matriksNormalisasi = $this->normalisasiMatriks($matriksKeputusan);
        
        // 4. Matriks terbobot
        $matriksTerbobot = $this->matriksTerbobot($matriksNormalisasi, $bobot);
        
        // 5. Solusi ideal positif dan negatif
        $solusiIdeal = $this->hitungSolusiIdeal($matriksTerbobot, $bobot);
        
        // 6. Jarak ke solusi ideal
        $jarak = $this->hitungJarak($matriksTerbobot, $solusiIdeal);
        
        // 7. Nilai preferensi (Ci)
        $nilaiPreferensi = $this->hitungNilaiPreferensi($jarak);
        
        // 8. Ranking
        $ranking = $this->hitungRanking($nilaiPreferensi);
        
        // 9. Status berdasarkan nilai preferensi
        $status = $this->tentukanStatus($nilaiPreferensi);
        
        // 10. Siapkan hasil akhir
        $hasil = [];
        foreach ($narapidanaIds as $index => $nId) {
            $hasil[] = [
                'narapidana_id' => $nId,
                'nama' => $narapidanaData[$nId]['nama'],
                'nomor_registrasi' => $narapidanaData[$nId]['nomor_registrasi'],
                'nilai_preferensi' => $nilaiPreferensi[$index],
                'ranking' => $ranking[$index],
                'status' => $status[$index],
                'jarak_positif' => $jarak['positif'][$index],
                'jarak_negatif' => $jarak['negatif'][$index]
            ];
        }
        
        // 11. Detail perhitungan untuk disimpan
        $detailPerhitungan = [
            'matriks_keputusan' => $matriksKeputusan,
            'matriks_normalisasi' => $matriksNormalisasi,
            'matriks_terbobot' => $matriksTerbobot,
            'solusi_ideal' => $solusiIdeal,
            'bobot' => $bobot,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return [
            'hasil' => $hasil,
            'detail' => $detailPerhitungan,
            'total_narapidana' => count($narapidanaIds),
            'total_kriteria' => count($subkriteriaList)
        ];
    }
    
    /**
     * Normalisasi matriks keputusan
     */
    private function normalisasiMatriks($matriks)
    {
        $m = count($matriks); // Jumlah alternatif
        $n = count($matriks[0]); // Jumlah kriteria
        
        $normalisasi = array_fill(0, $m, array_fill(0, $n, 0));
        
        // Untuk setiap kriteria
        for ($j = 0; $j < $n; $j++) {
            // Hitung sum of squares
            $sumSquares = 0;
            for ($i = 0; $i < $m; $i++) {
                $sumSquares += pow($matriks[$i][$j], 2);
            }
            
            $sqrtSum = sqrt($sumSquares);
            
            // Normalisasi
            for ($i = 0; $i < $m; $i++) {
                if ($sqrtSum > 0) {
                    $normalisasi[$i][$j] = $matriks[$i][$j] / $sqrtSum;
                }
            }
        }
        
        return $normalisasi;
    }
    
    /**
     * Matriks terbobot
     */
    private function matriksTerbobot($matriksNormalisasi, $bobot)
    {
        $m = count($matriksNormalisasi);
        $n = count($matriksNormalisasi[0]);
        
        $terbobot = array_fill(0, $m, array_fill(0, $n, 0));
        
        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $terbobot[$i][$j] = $matriksNormalisasi[$i][$j] * $bobot[$j]['bobot'];
            }
        }
        
        return $terbobot;
    }
    
    /**
     * Hitung solusi ideal positif dan negatif
     */
    private function hitungSolusiIdeal($matriksTerbobot, $bobot)
    {
        $m = count($matriksTerbobot);
        $n = count($matriksTerbobot[0]);
        
        $positif = array_fill(0, $n, 0);
        $negatif = array_fill(0, $n, 0);
        
        for ($j = 0; $j < $n; $j++) {
            $values = array_column($matriksTerbobot, $j);
            
            if ($bobot[$j]['jenis'] === 'Benefit') {
                $positif[$j] = max($values);
                $negatif[$j] = min($values);
            } else { // Cost
                $positif[$j] = min($values);
                $negatif[$j] = max($values);
            }
        }
        
        return [
            'positif' => $positif,
            'negatif' => $negatif
        ];
    }
    
    /**
     * Hitung jarak ke solusi ideal
     */
    private function hitungJarak($matriksTerbobot, $solusiIdeal)
    {
        $m = count($matriksTerbobot);
        $n = count($matriksTerbobot[0]);
        
        $jarakPositif = array_fill(0, $m, 0);
        $jarakNegatif = array_fill(0, $m, 0);
        
        for ($i = 0; $i < $m; $i++) {
            $sumPositif = 0;
            $sumNegatif = 0;
            
            for ($j = 0; $j < $n; $j++) {
                $sumPositif += pow($matriksTerbobot[$i][$j] - $solusiIdeal['positif'][$j], 2);
                $sumNegatif += pow($matriksTerbobot[$i][$j] - $solusiIdeal['negatif'][$j], 2);
            }
            
            $jarakPositif[$i] = sqrt($sumPositif);
            $jarakNegatif[$i] = sqrt($sumNegatif);
        }
        
        return [
            'positif' => $jarakPositif,
            'negatif' => $jarakNegatif
        ];
    }
    
    /**
     * Hitung nilai preferensi (Ci)
     */
    private function hitungNilaiPreferensi($jarak)
    {
        $m = count($jarak['positif']);
        $nilaiCi = array_fill(0, $m, 0);
        
        for ($i = 0; $i < $m; $i++) {
            $total = $jarak['negatif'][$i] + $jarak['positif'][$i];
            if ($total > 0) {
                $nilaiCi[$i] = $jarak['negatif'][$i] / $total;
            }
        }
        
        return $nilaiCi;
    }
    
    /**
     * Hitung ranking berdasarkan nilai preferensi
     */
    private function hitungRanking($nilaiPreferensi)
    {
        // Buat array dengan index dan nilai
        $data = [];
        foreach ($nilaiPreferensi as $index => $nilai) {
            $data[] = ['index' => $index, 'nilai' => $nilai];
        }
        
        // Urutkan descending berdasarkan nilai
        usort($data, function($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });
        
        // Beri ranking
        $ranking = array_fill(0, count($nilaiPreferensi), 0);
        $currentRank = 1;
        
        foreach ($data as $item) {
            $ranking[$item['index']] = $currentRank;
            $currentRank++;
        }
        
        return $ranking;
    }
    
    /**
     * Tentukan status berdasarkan nilai preferensi
     */
    private function tentukanStatus($nilaiPreferensi)
    {
        $status = [];
        
        foreach ($nilaiPreferensi as $nilai) {
            if ($nilai >= 0.8500) {
                $status[] = 'Remisi Penuh';
            } elseif ($nilai >= 0.7500) {
                $status[] = 'Remisi Separuh';
            } else {
                $status[] = 'Tidak Layak';
            }
        }
        
        return $status;
    }
    
    /**
     * Simpan hasil TOPSIS ke database
     */
    public function simpanHasil($periodeId, $hasilTopsis)
    {
        $db = \Config\Database::connect();
        
        // Hapus hasil lama untuk periode yang sama
        $db->table($this->table)->where('periode_id', $periodeId)->delete();
        
        // Simpan hasil baru
        $batchData = [];
        foreach ($hasilTopsis['hasil'] as $item) {
            $batchData[] = [
                'periode_id' => $periodeId,
                'narapidana_id' => $item['narapidana_id'],
                'nilai_preferensi' => $item['nilai_preferensi'],
                'ranking' => $item['ranking'],
                'status' => $item['status'],
                'detail_perhitungan' => json_encode($hasilTopsis['detail']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($batchData)) {
            return $db->table($this->table)->insertBatch($batchData);
        }
        
        return false;
    }
    
    /**
     * Get hasil TOPSIS untuk periode tertentu
     */
    public function getHasilTopsis($periodeId)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table($this->table . ' r');
        $builder->select('r.*, n.nama_lengkap, n.nomor_registrasi, p.nama_periode, p.tahun, p.bulan');
        $builder->join('narapidana n', 'n.id = r.narapidana_id');
        $builder->join('periode_penilaian p', 'p.id = r.periode_id');
        $builder->where('r.periode_id', $periodeId);
        $builder->orderBy('r.ranking', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get detail perhitungan TOPSIS
     */
    public function getDetailPerhitungan($rankingId)
    {
        $result = $this->find($rankingId);
        
        if ($result && !empty($result['detail_perhitungan'])) {
            return json_decode($result['detail_perhitungan'], true);
        }
        
        return null;
    }
    
    /**
     * Hitung TOPSIS otomatis untuk periode tertentu
     * Dipanggil ketika data penilaian lengkap
     */
    public function hitungOtomatis($periode)
    {
        // Cari periode ID dari string periode (YYYY-MM)
        $db = \Config\Database::connect();
        $periodeParts = explode('-', $periode);
        
        if (count($periodeParts) < 2) {
            return ['success' => false, 'message' => 'Format periode tidak valid'];
        }
        
        $tahun = $periodeParts[0];
        $bulan = $periodeParts[1];
        
        // Cari periode di database
        $builder = $db->table('periode_penilaian');
        $builder->where('tahun', $tahun);
        $builder->where('bulan', $bulan);
        $periodeData = $builder->get()->getRowArray();
        
        if (!$periodeData) {
            return ['success' => false, 'message' => 'Periode tidak ditemukan di database'];
        }
        
        $periodeId = $periodeData['id'];
        
        // Cek apakah sudah ada hasil TOPSIS untuk periode ini
        $builder = $db->table($this->table);
        $builder->where('periode_id', $periodeId);
        $existing = $builder->countAllResults();
        
        if ($existing > 0) {
            return ['success' => false, 'message' => 'TOPSIS sudah dihitung untuk periode ini'];
        }
        
        // Hitung TOPSIS
        $hasilTopsis = $this->hitungTopsis($periode);
        
        if (isset($hasilTopsis['error'])) {
            return ['success' => false, 'message' => $hasilTopsis['error']];
        }
        
        // Simpan hasil ke database
        $saved = $this->simpanHasil($periodeId, $hasilTopsis);
        
        if (!$saved) {
            return ['success' => false, 'message' => 'Gagal menyimpan hasil perhitungan'];
        }
        
        return [
            'success' => true,
            'message' => 'TOPSIS berhasil dihitung dan disimpan',
            'data' => $hasilTopsis
        ];
    }
    
    /**
     * Cek dan hitung TOPSIS otomatis untuk semua periode yang belum dihitung
     */
    public function cekDanHitungOtomatis()
    {
        $db = \Config\Database::connect();
        
        // Ambil semua periode yang memiliki data penilaian
        $builder = $db->table('periode_penilaian p');
        $builder->select('p.id, p.tahun, p.bulan, COUNT(pen.id) as jumlah_penilaian');
        $builder->join('penilaian pen', "pen.periode = CONCAT(p.tahun, '-', LPAD(p.bulan, 2, '0'))", 'left');
        $builder->groupBy('p.id, p.tahun, p.bulan');
        $builder->having('COUNT(pen.id) > 0');
        
        $periodes = $builder->get()->getResultArray();
        
        $results = [];
        foreach ($periodes as $periode) {
            $periodeId = $periode['id'];
            $periodeFormat = $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT);
            
            // Cek apakah sudah ada hasil TOPSIS
            $builder = $db->table($this->table);
            $builder->where('periode_id', $periodeId);
            $existing = $builder->countAllResults();
            
            if ($existing == 0) {
                // Hitung TOPSIS otomatis
                $result = $this->hitungOtomatis($periodeFormat);
                $results[] = [
                    'periode' => $periodeFormat,
                    'hasil' => $result
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Ambil jenis kriteria berdasarkan ID
     */
    private function getJenisKriteria($kriteriaId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('kriteria');
        $builder->select('jenis');
        $builder->where('id', $kriteriaId);
        $result = $builder->get()->getRowArray();
        
        return $result ? $result['jenis'] : 'Benefit';
    }
}