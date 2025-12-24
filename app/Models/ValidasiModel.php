<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidasiModel extends Model
{
    protected $table = 'validasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'periode',
        'narapidana_id',
        'status_validasi',
        'catatan',
        'validated_by',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'periode' => 'required|max_length[7]',
        'narapidana_id' => 'required|integer',
        'status_validasi' => 'required|in_list[menunggu,disetujui,perlu_review,ditolak]',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get validasi by periode
     */
    public function getValidasiByPeriode($periode)
    {
        return $this->select('validasi.*, narapidana.nama_lengkap, narapidana.nomor_registrasi, users.nama_lengkap as validator_nama')
            ->join('narapidana', 'narapidana.id = validasi.narapidana_id')
            ->join('users', 'users.id = validasi.validated_by', 'left')
            ->where('validasi.periode', $periode)
            ->orderBy('validasi.status_validasi', 'ASC')
            ->orderBy('validasi.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get validasi by narapidana_id and periode
     */
    public function getValidasiByNarapidanaPeriode($narapidana_id, $periode)
    {
        return $this->where('narapidana_id', $narapidana_id)
            ->where('periode', $periode)
            ->first();
    }

    /**
     * Save or update validasi
     */
    public function saveValidasi($data)
    {
        // Cek apakah sudah ada validasi untuk narapidana di periode ini
        $existing = $this->where('narapidana_id', $data['narapidana_id'])
            ->where('periode', $data['periode'])
            ->first();

        if ($existing) {
            // Update existing
            return $this->update($existing['id'], $data);
        } else {
            // Insert new
            return $this->insert($data);
        }
    }

    /**
     * Get statistik validasi
     */
    public function getStatistikValidasi($periode)
    {
        $result = $this->select('status_validasi, COUNT(*) as total')
            ->where('periode', $periode)
            ->groupBy('status_validasi')
            ->findAll();

        $statistik = [
            'menunggu' => 0,
            'disetujui' => 0,
            'perlu_review' => 0,
            'ditolak' => 0,
            'total' => 0
        ];

        foreach ($result as $row) {
            $statistik[$row['status_validasi']] = (int)$row['total'];
            $statistik['total'] += (int)$row['total'];
        }

        return $statistik;
    }

    /**
     * Get riwayat validasi (semua periode, urutan terbaru)
     */
    public function getRiwayatValidasi()
    {
        return $this->select('validasi.*, narapidana.nama_lengkap, narapidana.nomor_registrasi, users.nama_lengkap as validator_nama')
            ->join('narapidana', 'narapidana.id = validasi.narapidana_id')
            ->join('users', 'users.id = validasi.validated_by', 'left')
            ->orderBy('validasi.created_at', 'DESC')
            ->orderBy('validasi.periode', 'DESC')
            ->findAll();
    }
}
