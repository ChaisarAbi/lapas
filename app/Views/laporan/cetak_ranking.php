<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
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
            font-weight: normal;
        }
        .info {
            margin-bottom: 20px;
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
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .page-break {
            page-break-before: always;
        }
        .status-legend {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .status-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISTEM PENDUKUNG KEPUTUSAN PEMBINAAN NARAPIDANA</h1>
        <h2>LAPORAN RANKING NARAPIDANA</h2>
        <h3>Periode: <?= $periode ?></h3>
    </div>
    
    <div class="info">
        <table>
            <tr>
                <td width="30%">Tanggal Cetak</td>
                <td>: <?= $tanggal_cetak ?></td>
            </tr>
            <tr>
                <td>Total Narapidana</td>
                <td>: <?= count($ranking) ?> orang</td>
            </tr>
            <tr>
                <td>Total Kriteria</td>
                <td>: <?= count($kriteria) ?> kriteria</td>
            </tr>
            <tr>
                <td>Metode Perhitungan</td>
                <td>: TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</td>
            </tr>
        </table>
    </div>
    
    <!-- Status Legend -->
    <div class="status-legend">
        <strong>Legenda Status Remisi:</strong>
        <div class="status-item">
            <span class="badge badge-success">Remisi Penuh</span> - 30% terbaik
        </div>
        <div class="status-item">
            <span class="badge badge-warning">Remisi Separuh</span> - 30% berikutnya
        </div>
        <div class="status-item">
            <span class="badge badge-danger">Tidak Layak</span> - 40% terbawah
        </div>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th width="20%">Narapidana</th>
                <th width="10%">Kode</th>
                <th width="15%">Nilai S</th>
                <th width="15%">Nilai R</th>
                <th width="15%">Nilai Q</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ranking as $index => $row): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td>
                    <strong><?= $row['nama'] ?></strong><br>
                    <small class="text-muted">NIP: <?= $row['nip'] ?></small>
                </td>
                <td class="text-center"><?= $row['kode'] ?></td>
                <td class="text-center"><?= number_format($row['nilai_s'], 4) ?></td>
                <td class="text-center"><?= number_format($row['nilai_r'], 4) ?></td>
                <td class="text-center"><?= number_format($row['nilai_q'], 4) ?></td>
                <td class="text-center">
                    <span class="badge <?= $row['status_class'] ?>">
                        <i class="fas fa-star<?= $row['status'] == 'remisi_separuh' ? '-half-alt' : '' ?>"></i>
                        <?= $row['status_text'] ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak oleh: Administrator Sistem</p>
        <p>Tanggal: <?= date('d/m/Y H:i:s') ?></p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>