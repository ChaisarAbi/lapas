<?php

namespace App\Models;

use CodeIgniter\Model;

class KriteriaModel extends Model
{
    protected $table = 'kriteria';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'kode',
        'nama',
        'jenis',
        'bobot'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'kode' => 'required|min_length[2]|max_length[10]',
        'nama' => 'required|min_length[3]|max_length[100]',
        'jenis' => 'required|in_list[Benefit,Cost]',
        'bobot' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[1]'
    ];
    
    protected $validationMessages = [
        'kode' => [
            'required' => 'Kode kriteria harus diisi',
            'min_length' => 'Kode minimal 2 karakter',
            'max_length' => 'Kode maksimal 10 karakter',
            'is_unique' => 'Kode kriteria sudah digunakan'
        ],
        'nama' => [
            'required' => 'Nama kriteria harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'jenis' => [
            'required' => 'Jenis kriteria harus dipilih',
            'in_list' => 'Jenis kriteria tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    /**
     * Get total bobot kriteria
     */
    public function getTotalBobot()
    {
        $result = $this->selectSum('bobot')->first();
        return $result ? (float)$result['bobot'] : 0;
    }

    /**
     * Get kriteria benefit
     */
    public function getBenefit()
    {
        return $this->where('jenis', 'Benefit')->findAll();
    }

    /**
     * Get kriteria cost
     */
    public function getCost()
    {
        return $this->where('jenis', 'Cost')->findAll();
    }

    /**
     * Update bobot kriteria
     */
    public function updateBobot($id, $bobot)
    {
        return $this->update($id, ['bobot' => $bobot]);
    }

    /**
     * Get kriteria dengan urutan kode
     */
    public function getOrdered()
    {
        return $this->orderBy('kode', 'ASC')->findAll();
    }
}
