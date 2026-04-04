<?= $this->extend('guru/template/layout') ?>

<?= $this->section('content') ?>

<?php
$queryExport = http_build_query([
    'tahun_ajaran_id' => $filters['tahun_ajaran_id'] ?? '',
    'periode'         => $filters['periode'] ?? '',
    'status'          => $filters['status'] ?? '',
]);

// Fungsi untuk Badge Status dengan Icon
function badgeStatus($status)
{
    if ($status === 'lunas') {
        return '<span class="text-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sudah Bayar</span>';
    }

    if ($status === 'sebagian') {
        return '<span class="text-warning px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i> Sebagian</span>';
    }

    return '<span class="text-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Belum Bayar</span>';
}

// Fungsi untuk Mark Tabel Rekap dengan simbol sederhana
function statusMark($status)
{
    if ($status === 'lunas') {
        return '<span class="text-success font-weight-bold" title="Sudah Bayar" style="font-size: 1.25rem;">✓</span>';
    }

    return '<span class="text-danger font-weight-bold" title="Belum Lunas / Belum ada data" style="font-size: 1.25rem;">X</span>';
}
?>

<style>
    .zoomable-table {
        transition: font-size 0.3s ease-in-out;
        font-size: 1rem;
        /* Base size */
    }

    .table-dynamic th,
    .table-dynamic td {
        white-space: nowrap;
        /* Mencegah teks patah/turun ke bawah secara acak */
        vertical-align: middle;
    }

    .col-expand {
        width: 100%;
        /* Memaksa kolom ini mengambil sisa ruang yang tersedia */
        white-space: normal !important;
        /* Mengizinkan teks panjang membungkus (wrap) */
        min-width: 250px;
    }

    .col-fit {
        width: 1%;
        /* Memaksa kolom menyesuaikan dengan isi kontennya */
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><i class="fas fa-wallet text-primary mr-2"></i>Laporan SPP Siswa</h1>
        </div>
        <a href="<?= site_url('guru/laporan/export?' . $queryExport) ?>" class="btn btn-success shadow-sm">
            <i class="fas fa-file-excel mr-1"></i> Export Excel
        </a>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body bg-light rounded">
            <form method="get" action="<?= site_url('guru/laporan') ?>" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-control custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($tahunAjaranOptions as $ta): ?>
                                <option value="<?= $ta['id'] ?>" <?= ((string) ($filters['tahun_ajaran_id'] ?? '') === (string) $ta['id']) ? 'selected' : '' ?>>
                                    <?= esc($ta['nama_tahun_ajaran']) ?><?= ((int) $ta['is_active'] === 1) ? ' (Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label font-weight-bold"><i class="fas fa-clock mr-1"></i> Periode</label>
                        <select name="periode" class="form-control custom-select">
                            <option value="">Semua Periode</option>
                            <?php foreach ($periodeOptions as $key => $label): ?>
                                <option value="<?= esc($key) ?>" <?= ((string) ($filters['periode'] ?? '') === (string) $key) ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Status</label>
                        <select name="status" class="form-control custom-select">
                            <option value="">Semua Status</option>
                            <option value="sudah_bayar" <?= (($filters['status'] ?? '') === 'sudah_bayar') ? 'selected' : '' ?>>Sudah Bayar</option>
                            <option value="belum_bayar" <?= (($filters['status'] ?? '') === 'belum_bayar') ? 'selected' : '' ?>>Belum Bayar</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary flex-fill shadow-sm mr-2"><i class="fas fa-filter "></i> Filter</button>
                            <a href="<?= site_url('guru/laporan') ?>" class="btn btn-light border shadow-sm " style="margin-left: 10px; title=" Reset Filter">
                                <i class="fas fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <?php if (($viewMode ?? 'detail') === 'rekap'): ?>
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-primary shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($summary['total_siswa'] ?? 0) ?> Siswa</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-success shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lunas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($summary['sudah_bayar'] ?? 0) ?> Data</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-danger shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Belum Bayar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($summary['belum_bayar'] ?? 0) ?> Data</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary d-inline-block"><i class="fas fa-table mr-2"></i>Rekap SPP per Siswa</h6>
                    <?php if (!empty($selectedTahunAjaran)): ?>
                        <span class="badge badge-primary px-3 py-2 ml-2">TA: <?= esc($selectedTahunAjaran['nama_tahun_ajaran']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="btn-group btn-group-sm shadow-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="zoomTable(-0.1)" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
                    <button type="button" class="btn btn-outline-primary" onclick="resetZoom()" title="Reset Zoom"><i class="fas fa-compress"></i></button>
                    <button type="button" class="btn btn-outline-primary" onclick="zoomTable(0.1)" title="Zoom In"><i class="fas fa-search-plus"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div class="alert alert-secondary border-0 mb-4" role="alert">
                    <i class="fas fa-info-circle mr-2"></i> <strong>Keterangan:</strong>
                    <span class="text-success ml-2 font-weight-bold">✓ = Sudah Bayar</span>,
                    <span class="text-danger ml-2 font-weight-bold">X = Belum Lunas / Belum ada data</span>.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle table-dynamic zoomable-table">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th class="align-middle col-fit">No</th>
                                <th class="align-middle col-fit">Kelas</th>
                                <th class="align-middle col-fit">NIS</th>
                                <th class="align-middle text-left col-expand">Nama Siswa</th>
                                <th class="align-middle text-left">Orang Tua</th>
                                <?php foreach ($periodeOptions as $label): ?>
                                    <th class="align-middle col-fit"><?= esc($label) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $i => $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-light border border-secondary text-dark"><?= esc($row['nama_kelas']) ?></span>
                                        </td>
                                        <td class="text-center"><?= esc($row['nis']) ?></td>
                                        <td class="font-weight-bold"><?= esc($row['nama_siswa']) ?></td>
                                        <td><?= esc($row['nama_orang_tua']) ?></td>
                                        <?php foreach ($periodeOptions as $periodeKey => $label): ?>
                                            <?php $cell = $row['periode_statuses'][$periodeKey] ?? ['status' => 'tidak_ada']; ?>
                                            <td class="text-center align-middle"><?= statusMark($cell['status']) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= 5 + count($periodeOptions) ?>" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i><br>
                                        Data laporan tidak ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-info shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Tagihan Terbayar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($summary['total_tagihan'], 0, ',', '.') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-danger shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Tunggakan</div>
                                <div class="h5 mb-0 font-weight-bold text-danger">Rp <?= number_format($summary['total_tunggakan'], 0, ',', '.') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between border-bottom-0">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary d-inline-block"><i class="fas fa-list-alt mr-2"></i>Detail Pembayaran SPP</h6>
                    <?php if (!empty($selectedTahunAjaran)): ?>
                        <span class="badge badge-primary px-3 py-2 ml-2 shadow-sm rounded-pill"><i class="fas fa-calendar-check mr-1"></i> TA: <?= esc($selectedTahunAjaran['nama_tahun_ajaran']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="btn-group btn-group-sm shadow-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="zoomTable(-0.1)" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
                    <button type="button" class="btn btn-outline-danger" onclick="resetZoom()" title="Reset Zoom"><i class="fas fa-compress"></i></button>
                    <button type="button" class="btn btn-outline-primary" onclick="zoomTable(0.1)" title="Zoom In"><i class="fas fa-search-plus"></i></button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 table-dynamic zoomable-table">
                        <thead class="bg-light">
                            <tr class="text-uppercase text-dark text-xs font-weight-bold">
                                <th class="py-3 text-center col-fit">No</th>
                                <th class="py-3 text-center col-fit"></i> Periode</th>
                                <th class="py-3 col-expand"></i> Identitas Siswa</th>
                                <th class="py-3 text-right"></i> Tagihan</th>
                                <th class="py-3 text-right"> Terbayar</th>
                                <th class="py-3 text-right"></i> Sisa</th>
                                <th class="py-3 text-center col-fit"></i> Jatuh Tempo</th>
                                <th class="py-3 text-center col-fit"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $i => $row): ?>
                                    <?php
                                    $sisa = (float) $row['nominal_tagihan'] - (float) $row['nominal_terbayar'];
                                    $periodeKey = $row['tahun'] . '-' . str_pad((string) $row['bulan'], 2, '0', STR_PAD_LEFT);
                                    $periodeLabel = $periodeOptions[$periodeKey] ?? ($row['bulan'] . '/' . $row['tahun']);

                                    $rowClass = '';
                                    if ($row['status_pembayaran'] === 'lunas') {
                                        $rowClass = 'table-success';
                                    } elseif ($row['status_pembayaran'] === 'belum_bayar') {
                                        $rowClass = 'table-danger';
                                    } elseif ($row['status_pembayaran'] === 'sebagian') {
                                        $rowClass = 'table-warning';
                                    }
                                    ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td class="align-middle text-center py-3 font-weight-bold text-dark"><?= $i + 1 ?></td>

                                        <td class="align-middle text-center py-3">
                                            <span class="text-dark" style="font-size: 0.85em;"><?= esc($periodeLabel) ?></span>
                                        </td>

                                        <td class="align-middle py-3">
                                            <div class="font-weight-bold text-dark mb-1" style="font-size: 1.05em;"><?= esc($row['nama_siswa']) ?></div>
                                            <div class="small">
                                                <span class="text-dark">NIS : <?= esc($row['nis']) ?></span>
                                                <span class="text-secondary mx-2">|</span>
                                                <span class="badge badge-light border border-secondary text-dark"><?= esc($row['nama_kelas']) ?></span>
                                            </div>
                                        </td>

                                        <td class="align-middle text-right py-3 font-weight-bold text-dark">
                                            Rp <?= number_format((float) $row['nominal_tagihan'], 0, ',', '.') ?>
                                        </td>

                                        <td class="align-middle text-right py-3 font-weight-bold <?= $row['nominal_terbayar'] > 0 ? 'text-success' : 'text-dark' ?>">
                                            Rp <?= number_format((float) $row['nominal_terbayar'], 0, ',', '.') ?>
                                        </td>

                                        <td class="align-middle text-right py-3 font-weight-bold <?= $sisa > 0 ? 'text-danger' : 'text-dark' ?>">
                                            Rp <?= number_format($sisa, 0, ',', '.') ?>
                                        </td>

                                        <td class="align-middle text-center py-3 text-dark">
                                            <?php if (!empty($row['tanggal_jatuh_tempo'])): ?>
                                                <?= date('d M Y', strtotime($row['tanggal_jatuh_tempo'])) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>

                                        <td class="align-middle text-center py-3">
                                            <?= badgeStatus($row['status_pembayaran']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted bg-white">
                                        <div class="my-3">
                                            <i class="fas fa-box-open fa-4x text-gray-300 mb-3"></i>
                                            <h5 class="font-weight-normal text-gray-500">Belum ada detail pembayaran</h5>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    let currentZoom = 1;
    const zoomStep = 0.1;
    const minZoom = 0.3; // Batas minimum zoom (60%)
    const maxZoom = 1.5; // Batas maksimum zoom (150%)

    function zoomTable(step) {
        currentZoom += step;

        // Mencegah zoom terlalu kecil atau terlalu besar
        if (currentZoom < minZoom) currentZoom = minZoom;
        if (currentZoom > maxZoom) currentZoom = maxZoom;

        // Terapkan ke semua tabel yang memiliki class 'zoomable-table'
        document.querySelectorAll('.zoomable-table').forEach(table => {
            table.style.fontSize = currentZoom + 'rem';
        });
    }

    function resetZoom() {
        currentZoom = 1;
        document.querySelectorAll('.zoomable-table').forEach(table => {
            table.style.fontSize = '1rem';
        });
    }
</script>

<?= $this->endSection() ?>