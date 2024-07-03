<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Permintaan</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table, .table th, .table td {
            border: 1px solid black;
        }
        .table th, .table td {
            padding: 8px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Laporan Permintaan Barang</h1>
    <p>Periode: <?= date('d-m-Y', strtotime($start_date)) ?> s/d <?= date('d-m-Y', strtotime($end_date)) ?></p>

    <?php if(isset($permintaan) && !empty($permintaan)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Permintaan</th>
                    <th>Nama Pengaju</th>
                    <th>Tanggal Permintaan</th>
                    <th>Type Permintaan</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Jumlah yang Diminta</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permintaan as $perm): ?>
                    <?php $total_barang = 0; ?>
                    <?php foreach ($detail_permintaan[$perm['kode_permintaan']] as $detail): ?>
                        <tr>
                            <?php if ($detail === reset($detail_permintaan[$perm['kode_permintaan']])): ?>
                                <td rowspan="<?= count($detail_permintaan[$perm['kode_permintaan']]); ?>">
                                    <?= $perm['kode_permintaan']; ?>
                                </td>
                                <td rowspan="<?= count($detail_permintaan[$perm['kode_permintaan']]); ?>">
                                    <?= $perm['nama_pengaju']; ?>
                                </td>
                                <td rowspan="<?= count($detail_permintaan[$perm['kode_permintaan']]); ?>">
                                    <?= date('d-m-Y', strtotime($perm['tanggal_permintaan'])); ?>
                                </td>
                                <td rowspan="<?= count($detail_permintaan[$perm['kode_permintaan']]); ?>">
                                    <?= $perm['type_permintaan']; ?>
                                </td>
                            <?php endif; ?>

                            <td><?= $detail['kode_barang']; ?></td>
                            <td><?= $detail['nama_barang']; ?></td>
                            <td><?= $detail['satuan']; ?></td>
                            <td><?= $detail['jumlah_yang_diminta']; ?></td>
                            <td><?= $detail['keterangan']; ?></td>
                        </tr>
                        <?php $total_barang += $detail['jumlah_yang_diminta']; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="7" align="right"><strong>Total Barang Diminta:</strong></td>
                        <td><strong><?= $total_barang; ?></strong></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada data permintaan untuk periode ini.</p>
    <?php endif; ?>
</body>
</html>

<script> window.print() </script>
