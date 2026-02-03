<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\EdgeModel;
use App\Models\SubkriteriaModel;
use App\Models\PeriodeModel;

class CheckEdges extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'check:edges';
    protected $description = 'Check ANP edge relationships in database';

    public function run(array $params)
    {
        $edgeModel = new EdgeModel();
        $subkriteriaModel = new SubkriteriaModel();
        $periodeModel = new PeriodeModel();

        // Get active periode
        $periodeAktif = $periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif['id'] ?? null;

        // Get all subkriteria
        $subkriteria = $subkriteriaModel->getWithKriteria();
        
        CLI::write("=== Daftar Subkriteria ===", 'green');
        foreach ($subkriteria as $sk) {
            CLI::write("ID: {$sk['id']}\tKode: {$sk['kode']}\tNama: {$sk['nama']}\tKriteria: {$sk['kriteria_nama']}");
        }
        
        CLI::write("\n=== Daftar Edges ===", 'green');
        if ($periodeId) {
            $edges = $edgeModel->where('periode_id', $periodeId)->findAll();
            
            foreach ($edges as $edge) {
                $fromNode = array_column($subkriteria, null, 'id')[$edge['from_node_id']] ?? null;
                $toNode = array_column($subkriteria, null, 'id')[$edge['to_node_id']] ?? null;
                
                $fromInfo = $fromNode ? "From: {$fromNode['kode']} - {$fromNode['nama']}" : "";
                $toInfo = $toNode ? "To: {$toNode['kode']} - {$toNode['nama']}" : "";
                
                CLI::write("From ID: {$edge['from_node_id']}\t{$fromInfo}\tTo ID: {$edge['to_node_id']}\t{$toInfo}");
            }
        }

        CLI::write("\n=== Edge untuk KM1 ===", 'cyan');
        CLI::write("=== Sebagai From Node ===");
        
        if ($periodeId) {
            // Find KM1
            $km1 = null;
            foreach ($subkriteria as $sk) {
                if (strpos(strtolower($sk['nama']), 'pelatihan') !== false && strpos(strtolower($sk['nama']), 'keterampilan') !== false) {
                    $km1 = $sk;
                    break;
                }
            }
            
            if ($km1) {
                CLI::write("KM1 ID: {$km1['id']}\tKode: {$km1['kode']}\tNama: {$km1['nama']}");
                
                // Get edges from KM1
                $km1Edges = $edgeModel->where('from_node_id', $km1['id'])->where('periode_id', $periodeId)->findAll();
                foreach ($km1Edges as $edge) {
                    $toNode = array_column($subkriteria, null, 'id')[$edge['to_node_id']] ?? null;
                    $toInfo = $toNode ? "To: {$toNode['kode']} - {$toNode['nama']}" : "";
                    CLI::write("To ID: {$edge['to_node_id']}\t{$toInfo}");
                }
                
                // Get edges to KM1 (influencers)
                CLI::write("\n=== Sebagai To Node (Influencers) ===");
                $km1Influencers = $edgeModel->getInfluencerNodes($km1['id'], $periodeId);
                
                if (empty($km1Influencers)) {
                    CLI::write("Tidak ada influencer untuk KM1", 'red');
                } else {
                    foreach ($km1Influencers as $inf) {
                        CLI::write("From ID: {$inf['id']}\tFrom: {$inf['kode']} - {$inf['nama']}");
                    }
                }
            } else {
                CLI::write("KM1 tidak ditemukan!", 'red');
            }
        }
    }
}