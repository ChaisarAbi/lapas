<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
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
     * Generate data untuk laporan ranking
     */
    public function getDataLaporanRanking($periode = null)
    {
        if (!$periode) {
            $periode = date('Y-m');
        }

        $db = \Config\Database::connect();
        
        // Ambil data narapidana aktif
        $narapidana = $db->table('narapidana')
            ->where('status', 'Aktif')
            ->get()
            ->getResultArray();
        
        // Ambil data kriteria
        $kriteria = $db->table('kriteria')
            ->get()
            ->getResultArray();
        
        // Ambil data penilaian untuk periode tertentu
        $penilaian = $db->table('penilaian')
            ->select('penilaian.*, narapidana.nama_lengkap, narapidana.nomor_registrasi, kriteria.kode, kriteria.nama as kriteria_nama')
            ->join('narapidana', 'narapidana.id = penilaian.narapidana_id')
            ->join('kriteria', 'kriteria.id = penilaian.kriteria_id')
            ->where('penilaian.periode', $periode)
            ->get()
            ->getResultArray();
        
        return [
            'narapidana' => $narapidana,
            'kriteria' => $kriteria,
            'penilaian' => $penilaian,
            'periode' => $periode
        ];
    }

    /**
     * Generate data untuk laporan validasi
     */
    public function getDataLaporanValidasi($periode = null)
    {
        if (!$periode) {
            $periode = date('Y-m');
        }

        $db = \Config\Database::connect();
        
        // Ambil data validasi
        $validasi = $db->table('validasi')
            ->select('validasi.*, narapidana.nama_lengkap, narapidana.nomor_registrasi, users.nama_lengkap as validator_nama')
            ->join('narapidana', 'narapidana.id = validasi.narapidana_id')
            ->join('users', 'users.id = validasi.validated_by', 'left')
            ->where('validasi.periode', $periode)
            ->orderBy('validasi.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Hitung statistik
        $statistik = [
            'menunggu' => 0,
            'disetujui' => 0,
            'perlu_review' => 0,
            'ditolak' => 0,
            'total' => count($validasi)
        ];
        
        foreach ($validasi as $item) {
            $status = $item['status_validasi'];
            if (isset($statistik[$status])) {
                $statistik[$status]++;
            }
        }
        
        return [
            'validasi' => $validasi,
            'statistik' => $statistik,
            'periode' => $periode
        ];
    }

    /**
     * Generate data untuk laporan penilaian per petugas
     */
    public function getDataLaporanPenilaianPetugas($periode = null, $petugasId = null)
    {
        if (!$periode) {
            $periode = date('Y-m');
        }

        $db = \Config\Database::connect();
        
        $builder = $db->table('penilaian')
            ->select('penilaian.*, narapidana.nama_lengkap, narapidana.nomor_registrasi, 
                     kriteria.kode, kriteria.nama as kriteria_nama, users.nama_lengkap as penilai_nama')
            ->join('narapidana', 'narapidana.id = penilaian.narapidana_id')
            ->join('kriteria', 'kriteria.id = penilaian.kriteria_id')
            ->join('users', 'users.id = penilaian.penilai_id')
            ->where('penilaian.periode', $periode);
        
        if ($petugasId) {
            $builder->where('penilaian.penilai_id', $petugasId);
        }
        
        $penilaian = $builder->orderBy('penilaian.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Hitung statistik
        $statistik = [
            'total' => count($penilaian),
            'rata_rata' => 0,
            'baik' => 0,
            'cukup' => 0,
            'perhatian' => 0
        ];
        
        $totalNilai = 0;
        foreach ($penilaian as $item) {
            $nilai = (float)$item['nilai'];
            $totalNilai += $nilai;
            
            if ($nilai >= 70) {
                $statistik['baik']++;
            } elseif ($nilai >= 50) {
                $statistik['cukup']++;
            } else {
                $statistik['perhatian']++;
            }
        }
        
        if ($statistik['total'] > 0) {
            $statistik['rata_rata'] = $totalNilai / $statistik['total'];
        }
        
        return [
            'penilaian' => $penilaian,
            'statistik' => $statistik,
            'periode' => $periode
        ];
    }

    /**
     * Get list periode yang tersedia
     */
    public function getListPeriode()
    {
        $db = \Config\Database::connect();
        
        $periodeList = $db->table('penilaian')
            ->select('periode')
            ->groupBy('periode')
            ->orderBy('periode', 'DESC')
            ->get()
            ->getResultArray();
        
        return array_column($periodeList, 'periode');
    }

    /**
     * Get list petugas (BIMKEMASWAT)
     */
    public function getListPetugas()
    {
        $db = \Config\Database::connect();
        
        $petugas = $db->table('users')
            ->select('id, nama_lengkap, username')
            ->where('role', 'BIMKEMASWAT')
            ->orderBy('nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
        
        return $petugas;
    }
}
