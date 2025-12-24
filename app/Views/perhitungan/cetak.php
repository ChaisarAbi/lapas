<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan Ranking Narapidana' ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .info {
            margin-bottom: 15px;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info td {
            padding: 3px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .page-break {
            page-break-before: always;
        }
        @media print {
            .no-print {
                display: none;
            }
            .table th, .table td {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISTEM PENDUKUNG KEPUTUSAN PEMBINAAN NARAPIDANA</h1>
        <h2>LAPORAN RANKING NARAPIDANA</h2>
        <p>Periode: <?= $periode ?? '2025-12' ?></p>
    </div>
    
    <div class="info">
        <table>
            <tr>
                <td width="30%"><strong>Tanggal Cetak:</strong></td>
                <td><?= $tanggal_cetak ?? date('d/m/Y H:i:s') ?></td>
            </tr>
            <tr>
                <td><strong>Jumlah Narapidana:</strong></td>
                <td><?= count($narapidana ?? []) ?> orang</td>
            </tr>
            <tr>
                <td><strong>Jumlah Kriteria:</strong></td>
                <td><?= count($kriteria ?? []) ?> kriteria</td>
            </tr>
            <tr>
                <td><strong>Metode Perhitungan:</strong></td>
                <td>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</td>
            </tr>
        </table>
    </div>
    
    <?php if (!empty($ranking)): ?>
    <h3>HASIL RANKING NARAPIDANA</h3>
    <table class="table">
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th width="15%">No. Registrasi</th>
                <th width="25%">Nama Narapidana</th>
                <th width="20%">Kasus</th>
                <th width="10%">Jarak D+</th>
                <th width="10%">Jarak D-</th>
                <th width="10%">Nilai Preferensi</th>
                <th width="5%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ranking as $index => $item): ?>
            <tr>
                <td align="center">
                    <span class="badge badge-<?= 
                        $index == 0 ? 'success' : 
                        ($index == 1 ? 'info' : 
                        ($index == 2 ? 'warning' : 'secondary'))
                    ?>">
                        <?= $index + 1 ?>
                    </span>
                </td>
                <td><?= $item['narapidana']['nomor_registrasi'] ?></td>
                <td><?= $item['narapidana']['nama_lengkap'] ?></td>
                <td><?= $item['narapidana']['kasus'] ?></td>
                <td align="right"><?= number_format($item['d_positif'], 4) ?></td>
                <td align="right"><?= number_format($item['d_negatif'], 4) ?></td>
                <td align="center">
                    <span class="badge badge-<?= 
                        $item['preferensi'] >= 0.8 ? 'success' : 
                        ($item['preferensi'] >= 0.6 ? 'info' : 
                        ($item['preferensi'] >= 0.4 ? 'warning' : 'danger'))
                    ?>">
                        <?= number_format($item['preferensi'], 4) ?>
                    </span>
                </td>
                <td align="center">
                    <span class="badge badge-<?= 
                        $item['narapidana']['status'] == 'Aktif' ? 'danger' :
                        ($item['narapidana']['status'] == 'Bebas' ? 'success' : 'warning')
                    ?>">
                        <?= substr($item['narapidana']['status'], 0, 1) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="page-break"></div>
    
    <h3>KRITERIA PENILAIAN</h3>
    <table class="table">
        <thead>
            <tr>
                <th width="10%">Kode</th>
                <th width="40%">Nama Kriteria</th>
                <th width="15%">Jenis</th>
                <th width="15%">Bobot</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kriteria as $k): ?>
            <tr>
                <td><?= $k['kode'] ?></td>
                <td><?= $k['nama'] ?></td>
                <td align="center">
                    <span class="badge badge-<?= $k['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                        <?= $k['jenis'] ?>
                    </span>
                </td>
                <td align="right"><?= number_format($k['bobot'], 3) ?></td>
                <td>
                    <?php if ($k['jenis'] == 'Benefit'): ?>
                        Semakin tinggi semakin baik
                    <?php else: ?>
                        Semakin rendah semakin baik
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h3>INTERPRETASI HASIL</h3>
    <p>Metode TOPSIS menghasilkan ranking berdasarkan nilai preferensi (Ci) yang dihitung dengan rumus:</p>
    <p><strong>Ci = D- / (D+ + D-)</strong></p>
    <p>Dimana:</p>
    <ul>
        <li><strong>D+</strong>: Jarak ke solusi ideal positif (semakin kecil semakin baik)</li>
        <li><strong>D-</strong>: Jarak ke solusi ideal negatif (semakin besar semakin baik)</li>
    </ul>
    <p>Narapidana dengan nilai Ci tertinggi menempati peringkat teratas dan menjadi prioritas utama dalam program pembinaan.</p>
    
    <h3>REKOMENDASI</h3>
    <p>Berdasarkan hasil perhitungan TOPSIS, direkomendasikan:</p>
    <ol>
        <li>Narapidana pada peringkat 1-3 menjadi prioritas utama dalam program pembinaan intensif.</li>
        <li>Narapidana pada peringkat 4-6 mendapatkan program pembinaan reguler.</li>
        <li>Narapidana pada peringkat 7 ke bawah mendapatkan monitoring dan evaluasi berkala.</li>
    </ol>
    
    <?php else: ?>
    <div style="text-align: center; padding: 40px;">
        <h3>TIDAK ADA DATA UNTUK DICETAK</h3>
        <p>Tidak ada data penilaian untuk periode <?= $periode ?? '2025-12' ?></p>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Dicetak oleh: Sistem SPK-Pembinaan</p>
        <p>Halaman 1 dari 1</p>
    </div>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">Cetak Laporan</button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>
