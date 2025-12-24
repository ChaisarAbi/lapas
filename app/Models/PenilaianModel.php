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
        'kriteria_id',
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
        'kriteria_id' => 'required|integer',
        'nilai' => 'required|decimal',
        'periode' => 'required|min_length[6]|max_length[20]',
        'penilai_id' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'narapidana_id' => [
            'required' => 'Narapidana harus dipilih',
            'integer' => 'ID narapidana harus berupa angka'
        ],
        'kriteria_id' => [
            'required' => 'Kriteria harus dipilih',
            'integer' => 'ID kriteria harus berupa angka'
        ],
        'nilai' => [
            'required' => 'Nilai harus diisi',
            'decimal' => 'Nilai harus berupa angka desimal'
        ],
        'periode' => [
            'required' => 'Periode harus diisi',
            'min_length' => 'Periode minimal 6 karakter',
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
     * Get penilaian dengan detail narapidana dan kriteria
     */
    public function getPenilaianDetail($periode = null)
    {
        $builder = $this->db->table('penilaian p');
        $builder->select('p.*, n.nama_lengkap, n.nomor_registrasi, k.kode, k.nama as kriteria_nama, k.jenis, u.nama_lengkap as penilai_nama');
        $builder->join('narapidana n', 'n.id = p.narapidana_id');
        $builder->join('kriteria k', 'k.id = p.kriteria_id');
        $builder->join('users u', 'u.id = p.penilai_id');
        
        if ($periode) {
            $builder->where('p.periode', $periode);
        }
        
        $builder->orderBy('n.nama_lengkap', 'ASC');
        $builder->orderBy('k.kode', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get penilaian by narapidana dengan detail
     */
    public function getPenilaianByNarapidana($narapidana_id, $periode = null)
    {
        $builder = $this->db->table('penilaian p');
        $builder->select('p.*, n.nama_lengkap, n.nomor_registrasi, k.kode, k.nama as kriteria_nama, k.jenis, u.nama_lengkap as penilai_nama');
        $builder->join('narapidana n', 'n.id = p.narapidana_id');
        $builder->join('kriteria k', 'k.id = p.kriteria_id');
        $builder->join('users u', 'u.id = p.penilai_id');
        $builder->where('p.narapidana_id', $narapidana_id);
        
        if ($periode) {
            $builder->where('p.periode', $periode);
        }
        
        $builder->orderBy('p.periode', 'DESC');
        $builder->orderBy('k.kode', 'ASC');
        
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
        // Cek apakah sudah ada penilaian untuk narapidana, kriteria, dan periode yang sama
        $existing = $this->where('narapidana_id', $data['narapidana_id'])
                        ->where('kriteria_id', $data['kriteria_id'])
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
}
