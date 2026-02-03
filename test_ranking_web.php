<?php
// Simple test script to verify ranking page functionality

// Create a simple HTML test page
$html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Ranking Distance Calculation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .test-section h2 {
            color: #495057;
            margin-top: 0;
        }
        .test-link {
            display: block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .test-link:hover {
            background-color: #0056b3;
        }
        .test-description {
            margin: 5px 0 15px;
            color: #6c757d;
            font-size: 0.9em;
        }
        .notes {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: 3px;
        }
        .notes h3 {
            color: #856404;
            margin-top: 0;
        }
        .notes ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 0.9em;
        }
        .status {
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Test Ranking Distance Calculation</h1>
            <p>Verifikasi perhitungan jarak positif (D+) dan jarak negatif (D-) pada halaman ranking</p>
        </div>

        <div class="test-section">
            <h2>Ranking Page Tests</h2>
            
            <div class="test-item">
                <a href="http://localhost/kalapas/validasi" class="test-link" target="_blank">
                    <span class="status warning">⚠️</span> Halaman Validasi Kalapas
                </a>
                <p class="test-description">
                    Buka halaman validasi kalapas dan periksa apakah jarak positif (D+) dan jarak negatif (D-) muncul
                </p>
            </div>
            
            <div class="test-item">
                <a href="http://localhost/kalapas/preview-cetak" class="test-link" target="_blank">
                    <span class="status warning">⚠️</span> Preview Cetak Laporan Kalapas
                </a>
                <p class="test-description">
                    Buka preview cetak laporan kalapas dan periksa apakah kolom D+ dan D- terisi
                </p>
            </div>
            
            <div class="test-item">
                <a href="http://localhost/kalapas/ranking" class="test-link" target="_blank">
                    <span class="status warning">⚠️</span> Halaman Ranking Kalapas
                </a>
                <p class="test-description">
                    Buka halaman ranking kalapas dan periksa apakah jarak positif dan negatif ditampilkan
                </p>
            </div>
            
            <div class="test-item">
                <a href="http://localhost/wali/dashboard" class="test-link" target="_blank">
                    <span class="status warning">⚠️</span> Dashboard Wali Pemasyarakatan
                </a>
                <p class="test-description">
                    Buka dashboard wali dan periksa apakah kolom jarak muncul pada ranking
                </p>
            </div>
        </div>

        <div class="notes">
            <h3>Petunjuk Pengujian</h3>
            <ul>
                <li>Click pada setiap link di atas untuk membuka halaman yang akan diuji</li>
                <li>Pada setiap halaman, periksa apakah kolom <strong>Jarak Positif (D+)</strong> dan <strong>Jarak Negatif (D-)</strong> muncul</li>
                <li>Jika jarak tidak muncul atau kosong, periksa apakah ada data penilaian untuk periode yang dipilih</li>
                <li>Data penilaian harus sudah diinput oleh petugas bimkesmaswat</li>
                <li>Jika perlu, coba mengganti periode untuk melihat data penilaian</li>
            </ul>
        </div>

        <div class="notes">
            <h3>Perubahan yang Dilakukan</h3>
            <ul>
                <li><strong>KalapasController:</strong> Menambahkan default value untuk d_positif dan d_negatif di method hitungRankingSederhana</li>
                <li><strong>RankingController:</strong> Menambahkan default value untuk d_positif dan d_negatif di method hitungRankingSederhana</li>
                <li><strong>WaliController:</strong> Menambahkan implementasi distance calculation (d_positif dan d_negatif) ke hitungRankingSederhana</li>
            </ul>
        </div>

        <div class="footer">
            <p>Test script untuk verifikasi perhitungan jarak pada halaman ranking</p>
            <p>Silakan buka setiap link dan periksa hasilnya</p>
        </div>
    </div>
</body>
</html>
HTML;

// Save to file
file_put_contents('test_ranking.html', $html);

echo "✅ Test file created: test_ranking.html\n";
echo "📖 Please open this file in your browser to test the ranking distance calculation\n";
echo "🌐 File location: " . realpath('test_ranking.html') . "\n";
?>