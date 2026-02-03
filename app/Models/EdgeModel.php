<?php

namespace App\Models;

use CodeIgniter\Model;

class EdgeModel extends Model
{
    protected $table = 'anp_edges';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'periode_id',
        'from_node_id',
        'to_node_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'from_node_id' => 'required|integer',
        'to_node_id' => 'required|integer',
        'periode_id' => 'permit_empty|integer'
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get influencers by target node
     */
    public function getInfluencersByTarget($targetNodeId, $periodeId = null)
    {
        $builder = $this->where('to_node_id', $targetNodeId);
        
        if ($periodeId) {
            $builder->where('periode_id', $periodeId);
        }
        
        return $builder->findAll();
    }

    /**
     * Get targets by influencer node
     */
    public function getTargetsByInfluencer($influencerNodeId, $periodeId = null)
    {
        $builder = $this->where('from_node_id', $influencerNodeId);
        
        if ($periodeId) {
            $builder->where('periode_id', $periodeId);
        }
        
        return $builder->findAll();
    }

    /**
     * Get all edges for a periode
     */
    public function getEdgesByPeriode($periodeId)
    {
        return $this->where('periode_id', $periodeId)->findAll();
    }

    /**
     * Check if edge exists
     */
    public function edgeExists($fromNodeId, $toNodeId, $periodeId = null)
    {
        $builder = $this->where('from_node_id', $fromNodeId)
                       ->where('to_node_id', $toNodeId);
        
        if ($periodeId) {
            $builder->where('periode_id', $periodeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Save multiple edges
     */
    public function saveEdges($edges, $periodeId = null)
    {
        // Delete existing edges for this periode
        if ($periodeId) {
            $this->where('periode_id', $periodeId)->delete();
        }

        // Save new edges
        $saved = 0;
        foreach ($edges as $edge) {
            $edge['periode_id'] = $periodeId;
            if ($this->save($edge)) {
                $saved++;
            }
        }

        return $saved;
    }

    /**
     * Get to nodes for a specific from node
     */
    public function getToNodes($fromNodeId, $periodeId = null)
    {
        $builder = $this->where('from_node_id', $fromNodeId);
        
        if ($periodeId) {
            $builder->where('periode_id', $periodeId);
        }
        
        return $builder->findAll();
    }

    /**
     * Save edges for a specific from node (batch upsert)
     */
    public function saveEdgesForFromNode($fromNodeId, $toNodeIds, $periodeId = null)
    {
        // Hapus edges lama untuk from_node ini di periode aktif
        $deleteBuilder = $this->where('from_node_id', $fromNodeId);
        if ($periodeId) {
            $deleteBuilder->where('periode_id', $periodeId);
        }
        $deleteBuilder->delete();

        // Simpan edges baru
        $saved = 0;
        foreach ($toNodeIds as $toNodeId) {
            // Skip jika from_node sama dengan to_node
            if ($fromNodeId == $toNodeId) {
                continue;
            }
            
            $edgeData = [
                'periode_id' => $periodeId,
                'from_node_id' => $fromNodeId,
                'to_node_id' => $toNodeId
            ];
            
            if ($this->save($edgeData)) {
                $saved++;
            }
        }

        return $saved;
    }

    /**
     * Get influencer nodes for target (with node details) - optimized
     */
    public function getInfluencerNodes($targetNodeId, $periodeId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('anp_edges e')
            ->select('s.id, s.kode, s.nama, s.kriteria_id, k.nama as kriteria_nama')
            ->join('subkriteria s', 's.id = e.from_node_id')
            ->join('kriteria k', 'k.id = s.kriteria_id')
            ->where('e.to_node_id', $targetNodeId);
        
        if ($periodeId) {
            $builder->where('e.periode_id', $periodeId);
        }
        
        $builder->orderBy('s.kriteria_id', 'ASC')
                ->orderBy('s.kode', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get target nodes for influencer (with node details)
     */
    public function getTargetNodes($influencerNodeId, $periodeId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('anp_edges e')
            ->select('s.id, s.kode, s.nama, s.kriteria_id, k.nama as kriteria_nama')
            ->join('subkriteria s', 's.id = e.to_node_id')
            ->join('kriteria k', 'k.id = s.kriteria_id')
            ->where('e.from_node_id', $influencerNodeId);
        
        if ($periodeId) {
            $builder->where('e.periode_id', $periodeId);
        }
        
        $builder->orderBy('s.kriteria_id', 'ASC')
                ->orderBy('s.kode', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get all nodes with their relationships
     */
    public function getNetworkStructure($periodeId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('anp_edges e')
            ->select('e.*, 
                     s_from.kode as from_kode, s_from.nama as from_nama, 
                     s_to.kode as to_kode, s_to.nama as to_nama')
            ->join('subkriteria s_from', 's_from.id = e.from_node_id')
            ->join('subkriteria s_to', 's_to.id = e.to_node_id');
        
        if ($periodeId) {
            $builder->where('e.periode_id', $periodeId);
        }
        
        $builder->orderBy('e.from_node_id', 'ASC')
                ->orderBy('e.to_node_id', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}