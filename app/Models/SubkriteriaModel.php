<?php

namespace App\Models;

use CodeIgniter\Model;

class SubkriteriaModel extends Model
{
    protected $table            = 'subkriteria';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kriteria_id', 'kode', 'nama', 'bobot', 'jenis'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'kriteria_id' => 'required|integer',
        'kode'        => 'required|max_length[20]|is_unique[subkriteria.kode,kriteria_id,{kriteria_id}]',
        'nama'        => 'required|max_length[255]',
        'bobot'       => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[1]',
        'jenis'       => 'required|in_list[Benefit,Cost]'
    ];
    protected $validationMessages   = [
        'kode' => [
            'is_unique' => 'Kode subkriteria sudah digunakan untuk kriteria ini.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get subkriteria by kriteria_id
     */
    public function getByKriteria($kriteria_id)
    {
        return $this->where('kriteria_id', $kriteria_id)->findAll();
    }

    /**
     * Get total bobot subkriteria for a kriteria
     */
    public function getTotalBobotByKriteria($kriteria_id)
    {
        $result = $this->selectSum('bobot')->where('kriteria_id', $kriteria_id)->first();
        return $result ? (float)$result['bobot'] : 0;
    }

    /**
     * Get subkriteria with kriteria info
     */
    public function getWithKriteria()
    {
        return $this->select('subkriteria.*, kriteria.kode as kriteria_kode, kriteria.nama as kriteria_nama')
                    ->join('kriteria', 'kriteria.id = subkriteria.kriteria_id')
                    ->findAll();
    }

    /**
     * Get subkriteria by id with kriteria info
     */
    public function getByIdWithKriteria($id)
    {
        return $this->select('subkriteria.*, kriteria.kode as kriteria_kode, kriteria.nama as kriteria_nama')
                    ->join('kriteria', 'kriteria.id = subkriteria.kriteria_id')
                    ->where('subkriteria.id', $id)
                    ->first();
    }

    /**
     * Update bobot subkriteria
     */
    public function updateBobot($id, $bobot)
    {
        return $this->update($id, ['bobot' => $bobot]);
    }

    /**
     * Get subkriteria ordered by bobot descending
     */
    public function getOrderedByBobot($kriteria_id = null)
    {
        $builder = $this->orderBy('bobot', 'DESC');
        
        if ($kriteria_id) {
            $builder->where('kriteria_id', $kriteria_id);
        }
        
        return $builder->findAll();
    }
}
