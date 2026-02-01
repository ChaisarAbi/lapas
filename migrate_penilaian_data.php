<?php
// Script untuk memigrasi data penilaian dari kriteria ke subkriteria

// Koneksi database langsung
$host = 'localhost';
$dbname = 'penjara';
$username = 'root';
$password = 'leaveempty';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== MIGRASI DATA PENILAIAN KE SUBKRITERIA ===\n\n";
    
    // 1. Backup data lama
    echo "1. Membuat backup data penilaian lama...\n";
    $pdo->query("CREATE TABLE IF NOT EXISTS penilaian_backup AS SELECT * FROM penilaian");
    echo "   ✓ Backup dibuat di tabel 'penilaian_backup'\n\n";
    
    // 2. Ambil mapping kriteria -> subkriteria
    echo "2. Mengambil mapping kriteria -> subkriteria...\n";
    $stmt = $pdo->query("SELECT s.id as subkriteria_id, s.kriteria_id, s.kode as subkriteria_kode, s.bobot, s.jenis, k.kode as kriteria_kode 
                         FROM subkriteria s 
                         JOIN kriteria k ON k.id = s.kriteria_id 
                         ORDER BY s.kriteria_id, s.kode");
    $subkriteria = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $mapping = [];
    foreach ($subkriteria as $sub) {
        $kriteriaId = $sub['kriteria_id'];
        if (!isset($mapping[$kriteriaId])) {
            $mapping[$kriteriaId] = [];
        }
        $mapping[$kriteriaId][] = $sub;
    }
    
    echo "   Total subkriteria: " . count($subkriteria) . "\n";
    foreach ($mapping as $kriteriaId => $subs) {
        echo "   Kriteria {$kriteriaId} -> " . count($subs) . " subkriteria\n";
    }
    
    // 3. Ambil data penilaian lama
    echo "\n3. Mengambil data penilaian lama...\n";
    $stmt = $pdo->query("SELECT * FROM penilaian ORDER BY narapidana_id, kriteria_id");
    $penilaianLama = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Total data penilaian lama: " . count($penilaianLama) . "\n";
    
    // 4. Hapus data lama (tapi backup sudah ada)
    echo "\n4. Menghapus data penilaian lama...\n";
    $pdo->query("TRUNCATE TABLE penilaian");
    echo "   ✓ Data lama dihapus\n\n";
    
    // 5. Ubah struktur tabel (ubah kriteria_id menjadi subkriteria_id)
    echo "5. Mengubah struktur tabel...\n";
    try {
        $pdo->query("ALTER TABLE penilaian CHANGE kriteria_id subkriteria_id INT(11) NOT NULL");
        echo "   ✓ Kolom kriteria_id diubah menjadi subkriteria_id\n";
    } catch (Exception $e) {
        echo "   ⚠ Error mengubah struktur: " . $e->getMessage() . "\n";
        echo "   Mungkin struktur sudah berubah, melanjutkan...\n";
    }
    
    // 6. Migrasi data: untuk setiap penilaian lama, buat penilaian baru untuk setiap subkriteria
    echo "\n6. Memigrasi data ke format baru...\n";
    $totalBaru = 0;
    
    foreach ($penilaianLama as $dataLama) {
        $narapidanaId = $dataLama['narapidana_id'];
        $kriteriaId = $dataLama['kriteria_id'];
        $nilai = $dataLama['nilai'];
        $periode = $dataLama['periode'];
        $penilaiId = $dataLama['penilai_id'];
        $createdAt = $dataLama['created_at'];
        $updatedAt = $dataLama['updated_at'];
        
        // Cek apakah kriteria ini ada mapping ke subkriteria
        if (isset($mapping[$kriteriaId])) {
            foreach ($mapping[$kriteriaId] as $sub) {
                // Untuk saat ini, kita copy nilai yang sama ke semua subkriteria
                // Di production, mungkin perlu logika mapping yang lebih kompleks
                
                $sql = "INSERT INTO penilaian (narapidana_id, subkriteria_id, nilai, periode, penilai_id, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $narapidanaId,
                    $sub['subkriteria_id'],
                    $nilai,
                    $periode,
                    $penilaiId,
                    $createdAt,
                    $updatedAt
                ]);
                
                $totalBaru++;
                
                echo "   Migrasi: Narapidana {$narapidanaId}, Kriteria {$kriteriaId} -> Subkriteria {$sub['subkriteria_kode']} ({$sub['subkriteria_id']})\n";
            }
        } else {
            echo "   ⚠ Warning: Kriteria {$kriteriaId} tidak memiliki subkriteria mapping\n";
        }
    }
    
    echo "\n   Total data baru: {$totalBaru}\n";
    
    // 7. Verifikasi data
    echo "\n7. Verifikasi data...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM penilaian");
    $totalNew = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->query("SELECT COUNT(DISTINCT narapidana_id) as narapidana, COUNT(DISTINCT subkriteria_id) as subkriteria FROM penilaian");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   Total data penilaian baru: {$totalNew}\n";
    echo "   Jumlah narapidana: {$stats['narapidana']}\n";
    echo "   Jumlah subkriteria: {$stats['subkriteria']}\n";
    
    // 8. Cek apakah semua subkriteria terisi untuk setiap narapidana
    echo "\n8. Cek kelengkapan data...\n";
    $stmt = $pdo->query("SELECT p.narapidana_id, n.nama_lengkap, COUNT(p.subkriteria_id) as total_subkriteria
                         FROM penilaian p
                         JOIN narapidana n ON n.id = p.narapidana_id
                         WHERE p.periode = '2026-02'
                         GROUP BY p.narapidana_id, n.nama_lengkap");
    $kelengkapan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($kelengkapan as $data) {
        echo "   Narapidana {$data['nama_lengkap']}: {$data['total_subkriteria']} subkriteria\n";
    }
    
    echo "\n=== MIGRASI SELESAI ===\n";
    echo "Data penilaian berhasil dimigrasi dari kriteria ke subkriteria.\n";
    echo "Backup data lama tersedia di tabel 'penilaian_backup'.\n";
    echo "\nCatatan:\n";
    echo "1. Nilai kriteria lama di-copy ke semua subkriteria terkait\n";
    echo "2. Untuk penilaian yang lebih akurat, perlu input nilai per subkriteria\n";
    echo "3. Form input penilaian perlu diupdate untuk menampilkan subkriteria\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>