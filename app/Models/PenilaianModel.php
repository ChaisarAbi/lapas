<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table = 'penilaian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'narapidana_id',
        'subkriteria_id',
        'nilai',
        'periode',
        'penilai_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'narapidana_id' => 'required|integer',
        'subkriteria_id' => 'required|integer',
        'nilai' => 'required|decimal',
        'periode' => 'required|min_length[4]|max_length[20]',
        'penilai_id' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'narapidana_id' => [
            'required' => 'Narapidana harus dipilih',
            'integer' => 'ID narapidana harus berupa angka'
        ],
        'subkriteria_id' => [
            'required' => 'Subkriteria harus dipilih',
            'integer' => 'ID subkriteria harus berupa angka'
        ],
        'nilai' => [
            'required' => 'Nilai harus diisi',
            'decimal' => 'Nilai harus berupa angka desimal'
        ],
        'periode' => [
            'required' => 'Periode harus diisi',
            'min_length' => 'Periode minimal 4 karakter',
            'max_length' => 'Periode maksimal 20 karakter'
        ],
        'penilai_id' => [
            'required' => 'Penilai harus dipilih',
            'integer' => 'ID penilai harus berupa angka'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    /**
     * Get semua kriteria
     */
    public function getKriteria()
    {
        return $this->db->table('kriteria')
                       ->orderBy('kode', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get penilaian by periode
     */
    public function getPenilaianByPeriode($periode)
    {
        return $this->db->table('penilaian')
                       ->where('periode', $periode)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get penilaian by narapidana dan periode
     */
    public function getByNarapidanaPeriode($narapidana_id, $periode)
    {
        return $this->where('narapidana_id', $narapidana_id)
                   ->where('periode', $periode)
                   ->findAll();
    }

    /**
     * Get penilaian dengan detail narapidana dan subkriteria
     */
    public function getPenilaianDetail($periode = null)
    {
        $builder = $this->db->table('penilaian p');
        $builder->select('p.*, n.nama_lengkap, n.nomor_registrasi, s.kode, s.nama as subkriteria_nama, s.jenis, s.bobot, k.kode as kriteria_kode, k.nama as kriteria_nama, u.nama_lengkap as penilai_nama');
        $builder->join('narapidana n', 'n.id = p.narapidana_id');
        $builder->join('subkriteria s', 's.id = p.subkriteria_id');
        $builder->join('kriteria k', 'k.id = s.kriteria_id');
        $builder->join('users u', 'u.id = p.penilai_id');
        
        if ($periode) {
            $builder->where('p.periode', $periode);
        }
        
        $builder->orderBy('n.nama_lengkap', 'ASC');
        $builder->orderBy('k.kode', 'ASC');
        $builder->orderBy('s.kode', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get penilaian by narapidana dengan detail
     */
    public function getPenilaianByNarapidana($narapidana_id, $periode = null)
    {
        $builder = $this->db->table('penilaian p');
        $builder->select('p.*, n.nama_lengkap, n.nomor_registrasi, s.kode, s.nama as subkriteria_nama, s.jenis, s.bobot, k.kode as kriteria_kode, k.nama as kriteria_nama, u.nama_lengkap as penilai_nama');
        $builder->join('narapidana n', 'n.id = p.narapidana_id');
        $builder->join('subkriteria s', 's.id = p.subkriteria_id');
        $builder->join('kriteria k', 'k.id = s.kriteria_id');
        $builder->join('users u', 'u.id = p.penilai_id');
        $builder->where('p.narapidana_id', $narapidana_id);
        
        if ($periode) {
            $builder->where('p.periode', $periode);
        }
        
        $builder->orderBy('p.periode', 'DESC');
        $builder->orderBy('k.kode', 'ASC');
        $builder->orderBy('s.kode', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get penilaian grouped by narapidana
     */
    public function getPenilaianGroupedByNarapidana($periode = null)
    {
        $builder = $this->db->table('penilaian p');
        $builder->select('p.narapidana_id, n.nama_lengkap, n.nomor_registrasi, COUNT(p.id) as total_penilaian, MAX(p.created_at) as last_penilaian');
        $builder->join('narapidana n', 'n.id = p.narapidana_id');
        $builder->groupBy('p.narapidana_id, n.nama_lengkap, n.nomor_registrasi');
        
        if ($periode) {
            $builder->where('p.periode', $periode);
        }
        
        $builder->orderBy('n.nama_lengkap', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get periode yang tersedia dari tabel periode_penilaian
     */
    public function getPeriodeList()
    {
        return $this->db->table('periode_penilaian')
                       ->select('*')
                       ->orderBy('tahun', 'DESC')
                       ->orderBy('bulan', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get periode aktif untuk input penilaian
     */
    public function getPeriodeAktif()
    {
        return $this->db->table('periode_penilaian')
                       ->where('status', 'aktif')
                       ->get()
                       ->getRowArray();
    }

    /**
     * Get periode dalam format dropdown (YYYY-MM)
     */
    public function getPeriodeForDropdown()
    {
        $periodes = $this->getPeriodeList();
        $result = [];
        
        foreach ($periodes as $periode) {
            $key = $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT);
            $value = $periode['nama_periode'] . ' (' . $key . ')';
            $result[$key] = $value;
        }
        
        return $result;
    }

    /**
     * Simpan atau update penilaian
     */
    public function savePenilaian($data)
    {
        // Cek apakah sudah ada penilaian untuk narapidana, subkriteria, dan periode yang sama
        $existing = $this->where('narapidana_id', $data['narapidana_id'])
                        ->where('subkriteria_id', $data['subkriteria_id'])
                        ->where('periode', $data['periode'])
                        ->first();
        
        if ($existing) {
            // Update existing
            $data['id'] = $existing['id'];
            return $this->save($data);
        } else {
            // Insert new
            return $this->insert($data);
        }
    }

    /**
     * Konversi nilai 0-100 ke skala 1-5 untuk kriteria benefit
     */
    public function konversiSkalaBenefit($nilai)
    {
        $nilai = floatval($nilai);
        
        if ($nilai >= 0 && $nilai <= 20) return 1;
        if ($nilai >= 21 && $nilai <= 40) return 2;
        if ($nilai >= 41 && $nilai <= 60) return 3;
        if ($nilai >= 61 && $nilai <= 80) return 4;
        if ($nilai >= 81 && $nilai <= 100) return 5;
        
        return 0; // Nilai di luar range
    }

    /**
     * Konversi nilai 0-100 ke skala 1-3 untuk kriteria cost
     */
    public function konversiSkalaCost($nilai)
    {
        $nilai = floatval($nilai);
        
        if ($nilai >= 0 && $nilai <= 33) return 1;
        if ($nilai >= 34 && $nilai <= 66) return 2;
        if ($nilai >= 67 && $nilai <= 100) return 3;
        
        return 0; // Nilai di luar range
    }

    /**
     * Konversi nilai berdasarkan jenis kriteria
     */
    public function konversiNilai($nilai, $jenis_kriteria)
    {
        if ($jenis_kriteria === 'Benefit') {
            return $this->konversiSkalaBenefit($nilai);
        } elseif ($jenis_kriteria === 'Cost') {
            return $this->konversiSkalaCost($nilai);
        }
        
        return $nilai; // Return as-is jika jenis tidak dikenali
    }

    /**
     * Get penilaian dengan nilai yang sudah dikonversi
     */
    public function getPenilaianDenganKonversi($periode = null)
    {
        $penilaian = $this->getPenilaianDetail($periode);
        
        foreach ($penilaian as &$item) {
            $item['nilai_konversi'] = $this->konversiNilai($item['nilai'], $item['jenis']);
        }
        
        return $penilaian;
    }
    
    /**
     * Get penilaian untuk perhitungan TOPSIS (matriks keputusan)
     */
    public function getMatriksKeputusan($periode)
    {
        $builder = $this->db->table('penilaian p');
        $builder->select('p.narapidana_id, n.nama_lengkap, s.id as subkriteria_id, s.kode as subkriteria_kode, s.jenis, s.bobot, p.nilai');
        $builder->join('narapidana n', 'n.id = p.narapidana_id');
        $builder->join('subkriteria s', 's.id = p.subkriteria_id');
        $builder->where('p.periode', $periode);
        $builder->orderBy('n.nama_lengkap', 'ASC');
        $builder->orderBy('s.kode', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        // Format data menjadi matriks keputusan
        $matriks = [];
        $narapidana_list = [];
        $subkriteria_list = [];
        
        foreach ($result as $row) {
            $narapidana_id = $row['narapidana_id'];
            $subkriteria_id = $row['subkriteria_id'];
            
            if (!in_array($narapidana_id, $narapidana_list)) {
                $narapidana_list[] = $narapidana_id;
            }
            
            if (!in_array($subkriteria_id, $subkriteria_list)) {
                $subkriteria_list[] = $subkriteria_id;
            }
            
            $matriks[$narapidana_id][$subkriteria_id] = [
                'nilai' => $row['nilai'],
                'jenis' => $row['jenis'],
                'bobot' => $row['bobot'],
                'narapidana_nama' => $row['nama_lengkap'],
                'subkriteria_kode' => $row['subkriteria_kode']
            ];
        }
        
        return [
            'matriks' => $matriks,
            'narapidana_list' => $narapidana_list,
            'subkriteria_list' => $subkriteria_list
        ];
    }
}
