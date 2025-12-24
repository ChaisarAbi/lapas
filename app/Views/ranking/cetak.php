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
            padding: 5px;
            vertical-align: top;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LEMBAGA PEMASYARAKATAN</h1>
        <h2>SISTEM PENDUKUNG KEPUTUSAN PEMBINAAN NARAPIDANA</h2>
        <h3>LAPORAN RANKING NARAPIDANA</h3>
    </div>
    
    <div class="info">
        <table>
            <tr>
                <td width="50%">
                    <strong>Periode:</strong> <?= $periode ?><br>
                    <strong>Tanggal Cetak:</strong> <?= $tanggal_cetak ?>
                </td>
                <td width="50%" class="text-right">
                    <strong>Total Narapidana:</strong> <?= count($narapidana) ?><br>
                    <strong>Total Kriteria:</strong> <?= count($kriteria) ?>
                </td>
            </tr>
        </table>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th width="25%">Nama Narapidana</th>
                <th width="15%">Nomor Registrasi</th>
                <th width="20%">Kasus</th>
                <th width="15%">Jarak Positif (D+)</th>
                <th width="15%">Jarak Negatif (D-)</th>
                <th width="15%">Nilai Preferensi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ranking as $index => $item): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><?= $item['narapidana']['nama_lengkap'] ?></td>
                <td><?= $item['narapidana']['nomor_registrasi'] ?></td>
                <td><?= $item['narapidana']['kasus'] ?? '-' ?></td>
                <td class="text-center"><?= number_format($item['d_positif'], 4) ?></td>
                <td class="text-center"><?= number_format($item['d_negatif'], 4) ?></td>
                <td class="text-center">
                    <?php if ($item['preferensi'] >= 0.7): ?>
                        <span class="badge badge-success"><?= number_format($item['preferensi'], 4) ?></span>
                    <?php elseif ($item['preferensi'] >= 0.5): ?>
                        <span class="badge badge-warning"><?= number_format($item['preferensi'], 4) ?></span>
                    <?php else: ?>
                        <span class="badge badge-danger"><?= number_format($item['preferensi'], 4) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak oleh: Sistem SPK Pembinaan Narapidana</p>
        <p>Tanggal: <?= date('d/m/Y H:i:s') ?></p>
    </div>
</body>
</html>
