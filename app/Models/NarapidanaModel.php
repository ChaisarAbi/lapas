<?php

namespace App\Models;

use CodeIgniter\Model;

class NarapidanaModel extends Model
{
    protected $table = 'narapidana';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nomor_registrasi', 
        'nama_lengkap', 
        'jenis_kelamin', 
        'tempat_lahir', 
        'tanggal_lahir',
        'alamat',
        'kasus',
        'masa_tahanan',
        'tanggal_masuk',
        'status',
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
        'nomor_registrasi' => 'required|min_length[3]|max_length[50]',
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
        'tempat_lahir' => 'required|min_length[3]|max_length[50]',
        'tanggal_lahir' => 'required|valid_date',
        'alamat' => 'required|min_length[5]|max_length[255]',
        'kasus' => 'required|min_length[3]|max_length[255]',
        'masa_tahanan' => 'required|integer|greater_than[0]',
        'tanggal_masuk' => 'required|valid_date',
        'status' => 'required|in_list[Aktif,Bebas,Pindah]'
    ];
    
    protected $validationMessages = [
        'nomor_registrasi' => [
            'required' => 'Nomor registrasi harus diisi',
            'min_length' => 'Nomor registrasi minimal 3 karakter',
            'max_length' => 'Nomor registrasi maksimal 50 karakter',
            'is_unique' => 'Nomor registrasi sudah digunakan'
        ],
        'nama_lengkap' => [
            'required' => 'Nama lengkap harus diisi',
            'min_length' => 'Nama lengkap minimal 3 karakter',
            'max_length' => 'Nama lengkap maksimal 100 karakter'
        ],
        'jenis_kelamin' => [
            'required' => 'Jenis kelamin harus dipilih',
            'in_list' => 'Jenis kelamin tidak valid'
        ],
        'tempat_lahir' => [
            'required' => 'Tempat lahir harus diisi',
            'min_length' => 'Tempat lahir minimal 3 karakter',
            'max_length' => 'Tempat lahir maksimal 50 karakter'
        ],
        'tanggal_lahir' => [
            'required' => 'Tanggal lahir harus diisi',
            'valid_date' => 'Tanggal lahir tidak valid'
        ],
        'alamat' => [
            'required' => 'Alamat harus diisi',
            'min_length' => 'Alamat minimal 5 karakter',
            'max_length' => 'Alamat maksimal 255 karakter'
        ],
        'kasus' => [
            'required' => 'Kasus harus diisi',
            'min_length' => 'Kasus minimal 3 karakter',
            'max_length' => 'Kasus maksimal 255 karakter'
        ],
        'masa_tahanan' => [
            'required' => 'Masa tahanan harus diisi',
            'integer' => 'Masa tahanan harus berupa angka',
            'greater_than' => 'Masa tahanan harus lebih dari 0'
        ],
        'tanggal_masuk' => [
            'required' => 'Tanggal masuk harus diisi',
            'valid_date' => 'Tanggal masuk tidak valid'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    /**
     * Get narapidana dengan status tertentu
     */
    public function getByStatus($status = null)
    {
        if ($status) {
            return $this->where('status', $status)->findAll();
        }
        return $this->findAll();
    }

    /**
     * Get narapidana aktif
     */
    public function getAktif()
    {
        return $this->where('status', 'Aktif')->findAll();
    }

    /**
     * Search narapidana
     */
    public function search($keyword)
    {
        return $this->like('nomor_registrasi', $keyword)
                    ->orLike('nama_lengkap', $keyword)
                    ->orLike('kasus', $keyword)
                    ->findAll();
    }
}
