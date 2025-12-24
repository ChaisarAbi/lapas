<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodeModel extends Model
{
    protected $table = 'periode_penilaian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama_periode',
        'tahun',
        'bulan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nama_periode' => 'required|min_length[3]|max_length[100]',
        'tahun' => 'required|integer|greater_than[2020]',
        'bulan' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
        'status' => 'required|in_list[aktif,nonaktif,selesai]',
        'keterangan' => 'permit_empty|max_length[500]'
    ];
    
    protected $validationMessages = [
        'nama_periode' => [
            'required' => 'Nama periode harus diisi',
            'min_length' => 'Nama periode minimal 3 karakter',
            'max_length' => 'Nama periode maksimal 100 karakter'
        ],
        'tahun' => [
            'required' => 'Tahun harus diisi',
            'integer' => 'Tahun harus berupa angka',
            'greater_than' => 'Tahun harus lebih besar dari 2020'
        ],
        'bulan' => [
            'required' => 'Bulan harus diisi',
            'integer' => 'Bulan harus berupa angka',
            'greater_than_equal_to' => 'Bulan minimal 1',
            'less_than_equal_to' => 'Bulan maksimal 12'
        ],
        'tanggal_mulai' => [
            'required' => 'Tanggal mulai harus diisi',
            'valid_date' => 'Tanggal mulai tidak valid'
        ],
        'tanggal_selesai' => [
            'required' => 'Tanggal selesai harus diisi',
            'valid_date' => 'Tanggal selesai tidak valid'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status harus salah satu dari: aktif, nonaktif, selesai'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    /**
     * Get periode aktif
     */
    public function getAktif()
    {
        return $this->where('status', 'aktif')->first();
    }

    /**
     * Get semua periode dengan urutan terbaru
     */
    public function getAll()
    {
        return $this->orderBy('tahun', 'DESC')
                   ->orderBy('bulan', 'DESC')
                   ->findAll();
    }

    /**
     * Get periode by tahun dan bulan
     */
    public function getByTahunBulan($tahun, $bulan)
    {
        return $this->where('tahun', $tahun)
                   ->where('bulan', $bulan)
                   ->first();
    }

    /**
     * Format periode untuk dropdown (YYYY-MM)
     */
    public function getForDropdown()
    {
        $periodes = $this->orderBy('tahun', 'DESC')
                        ->orderBy('bulan', 'DESC')
                        ->findAll();
        
        $result = [];
        foreach ($periodes as $periode) {
            $result[$periode['id']] = $periode['nama_periode'] . ' (' . $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT) . ')';
        }
        
        return $result;
    }

    /**
     * Cek apakah periode aktif ada
     */
    public function hasActivePeriod()
    {
        return $this->where('status', 'aktif')->countAllResults() > 0;
    }

    /**
     * Nonaktifkan semua periode kecuali yang ditentukan
     */
    public function deactivateOthers($exceptId)
    {
        return $this->where('id !=', $exceptId)
                   ->set('status', 'nonaktif')
                   ->update();
    }
}
