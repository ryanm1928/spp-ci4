<?php
function exportStatusLabel($status)
{
    if ($status === 'lunas') {
        return 'Sudah Bayar';
    }

    if ($status === 'sebagian') {
        return 'Sebagian';
    }

    return 'Belum Bayar';
}

function exportStatusMark($status)
{
    return $status === 'lunas' ? '✓' : 'X';
}
?>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan SPP Siswa</title>
</head>

<body>
    <h2>Laporan SPP Siswa</h2>

    <table border="0" cellpadding="4">
        <tr>
            <td><strong>Tahun Ajaran</strong></td>
            <td>:</td>
            <td><?= esc($selectedTahunAjaran['nama_tahun_ajaran'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>:</td>
            <td>
                <?php
                if (!empty($filters['periode']) && isset($periodeOptions[$filters['periode']])) {
                    echo esc($periodeOptions[$filters['periode']]);
                } else {
                    echo 'Semua Periode';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>:</td>
            <td>
                <?php
                if (($filters['status'] ?? '') === 'sudah_bayar') {
                    echo 'Sudah Bayar';
                } elseif (($filters['status'] ?? '') === 'belum_bayar') {
                    echo 'Belum Bayar';
                } elseif (($filters['status'] ?? '') === 'sebagian') {
                    echo 'Sebagian';
                } else {
                    echo 'Semua';
                }
                ?>
            </td>
        </tr>
    </table>

    <br>

    <?php if (($viewMode ?? 'detail') === 'rekap'): ?>
        <table border="0" cellpadding="4">
            <tr>
                <td><strong>Total Siswa</strong></td>
                <td>:</td>
                <td><?= number_format($summary['total_siswa'] ?? 0) ?></td>
            </tr>
            <tr>
                <td><strong>Jumlah Bulan</strong></td>
                <td>:</td>
                <td><?= number_format($summary['total_periode'] ?? 0) ?></td>
            </tr>
            <tr>
                <td><strong>Lunas</strong></td>
                <td>:</td>
                <td><?= number_format($summary['sudah_bayar'] ?? 0) ?></td>
            </tr>
            <tr>
                <td><strong>Belum Bayar</strong></td>
                <td>:</td>
                <td><?= number_format($summary['belum_bayar'] ?? 0) ?></td>
            </tr>
            <tr>
                <td><strong>Sebagian</strong></td>
                <td>:</td>
                <td><?= number_format($summary['sebagian'] ?? 0) ?></td>
            </tr>
        </table>

        <br>

        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Orang Tua</th>
                    <?php foreach ($periodeOptions as $label): ?>
                        <th><?= esc($label) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['nama_kelas']) ?></td>
                            <td><?= esc($row['nis']) ?></td>
                            <td><?= esc($row['nama_siswa']) ?></td>
                            <td><?= esc($row['nama_orang_tua']) ?></td>
                            <?php foreach ($periodeOptions as $periodeKey => $label): ?>
                                <?php $cell = $row['periode_statuses'][$periodeKey] ?? ['status' => 'tidak_ada']; ?>
                                <td align="center"><?= exportStatusMark($cell['status']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= 5 + count($periodeOptions) ?>" align="center">Data tidak ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br>
        <table border="0" cellpadding="4">
            <tr>
                <td><strong>Keterangan</strong></td>
                <td>:</td>
                <td>✓ = Sudah Bayar, X = Belum Lunas / Belum ada data</td>
            </tr>
        </table>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tahun Ajaran</th>
                    <th>Periode</th>
                    <th>Kelas</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Orang Tua</th>
                    <th>Nominal Tagihan</th>
                    <th>Nominal Terbayar</th>
                    <th>Sisa Tagihan</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Tanggal Bayar Terakhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $i => $row): ?>
                        <?php
                        $sisa = (float) $row['nominal_tagihan'] - (float) $row['nominal_terbayar'];
                        $periodeKey = $row['tahun'] . '-' . str_pad((string) $row['bulan'], 2, '0', STR_PAD_LEFT);
                        $periodeLabel = $periodeOptions[$periodeKey] ?? ($row['bulan'] . '/' . $row['tahun']);
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['nama_tahun_ajaran']) ?></td>
                            <td><?= esc($periodeLabel) ?></td>
                            <td><?= esc($row['nama_kelas']) ?></td>
                            <td><?= esc($row['nis']) ?></td>
                            <td><?= esc($row['nama_siswa']) ?></td>
                            <td><?= esc($row['nama_orang_tua']) ?></td>
                            <td><?= number_format((float) $row['nominal_tagihan'], 0, ',', '.') ?></td>
                            <td><?= number_format((float) $row['nominal_terbayar'], 0, ',', '.') ?></td>
                            <td><?= number_format($sisa, 0, ',', '.') ?></td>
                            <td><?= !empty($row['tanggal_jatuh_tempo']) ? date('d-m-Y', strtotime($row['tanggal_jatuh_tempo'])) : '-' ?></td>
                            <td><?= !empty($row['tanggal_bayar_terakhir']) ? date('d-m-Y H:i', strtotime($row['tanggal_bayar_terakhir'])) : '-' ?></td>
                            <td><?= exportStatusLabel($row['status_pembayaran']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13" align="center">Data tidak ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br>

        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <td><strong>Total Data</strong></td>
                <td><?= number_format($summary['total_data']) ?></td>
            </tr>
            <tr>
                <td><strong>Sudah Bayar</strong></td>
                <td><?= number_format($summary['sudah_bayar']) ?></td>
            </tr>
            <tr>
                <td><strong>Belum Bayar</strong></td>
                <td><?= number_format($summary['belum_bayar']) ?></td>
            </tr>
            <tr>
                <td><strong>Sebagian</strong></td>
                <td><?= number_format($summary['sebagian']) ?></td>
            </tr>
            <tr>
                <td><strong>Total Tagihan</strong></td>
                <td><?= number_format($summary['total_tagihan'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td><strong>Total Terbayar</strong></td>
                <td><?= number_format($summary['total_terbayar'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td><strong>Total Tunggakan</strong></td>
                <td><?= number_format($summary['total_tunggakan'], 0, ',', '.') ?></td>
            </tr>
        </table>
    <?php endif; ?>
</body>

</html>