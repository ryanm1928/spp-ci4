<?= $this->extend('guru/template/layout') ?>

<?= $this->section('content') ?>

<?php
$bulanMap = $bulanMap ?? [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

$rupiah = static function ($value) {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};

$periodeLabel = '-';
if (! empty($selectedBulan) && ! empty($selectedTahun)) {
    $periodeLabel = ($bulanMap[(int) $selectedBulan] ?? $selectedBulan) . ' ' . $selectedTahun;
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Global & Typography */
    body {
        color: #334155;
    }

    /* Page Header - Diperbagus dengan gradien modern dan shadow */
    .page-header-spp {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        color: #f8fafc;
        padding: 32px 28px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, .15);
        margin-bottom: 24px;
        border-left: 6px solid #0d6efd;
    }

    .page-header-spp h3 {
        margin-bottom: 16px;
        font-weight: 700;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
    }

    .info-chip {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 8px;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        margin-right: 10px;
        margin-bottom: 10px;
        font-size: 13px;
        font-weight: 500;
        transition: background 0.2s;
    }

    .info-chip:hover {
        background: rgba(255, 255, 255, .2);
    }

    .info-chip i {
        color: #93c5fd;
    }

    /* Cards */
    .soft-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        background: #fff;
        margin-bottom: 24px;
    }

    .soft-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 700;
        color: #0f172a;
        padding: 20px 24px;
        border-radius: 16px 16px 0 0;
        font-size: 16px;
    }

    .toolbar-card {
        border-radius: 16px;
        background: #fff;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }

    /* Metrics */
    .metric-card {
        border-radius: 16px;
        padding: 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        height: 100%;
        transition: transform 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
    }

    .metric-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-value {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .metric-sub {
        margin-top: 12px;
        color: #94a3b8;
        font-size: 13px;
    }

    /* Stepper / Progress Bar Timeline */
    .timeline-stepper {
        display: flex;
        align-items: flex-start;
        overflow-x: auto;
        padding: 10px 5px 20px;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .timeline-stepper::-webkit-scrollbar {
        height: 6px;
    }

    .timeline-stepper::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }

    .timeline-step {
        flex: 0 0 auto;
        min-width: 130px;
        text-align: center;
        position: relative;
        cursor: pointer;
    }

    .timeline-step::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 50%;
        width: 100%;
        height: 3px;
        background: #f1f5f9;
        z-index: 1;
        transition: all 0.3s;
    }

    .timeline-step:last-child::before {
        display: none;
    }

    .step-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #e2e8f0;
        margin: 0 auto 12px;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        transition: all 0.3s ease;
    }

    .timeline-step:hover .step-circle {
        border-color: #93c5fd;
        color: #3b82f6;
    }

    .timeline-step.active .step-circle {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        box-shadow: 0 0 0 5px rgba(13, 110, 253, 0.15);
    }

    .timeline-step.active~.timeline-step .step-circle {
        background: #fff;
        border-color: #e2e8f0;
    }

    .step-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s ease;
    }

    .timeline-step.active .step-label {
        color: #0d6efd;
        font-weight: 700;
    }

    /* Modern Tables - Ditambahkan Border agar mudah dipahami awam */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        transition: font-size 0.3s ease;
        /* Transisi untuk fitur Zoom */
    }

    .table-modern thead th {
        position: sticky;
        /* Menahan header tetap di atas */
        top: 0;
        z-index: 10;
        /* Memastikan header berada di atas baris data */
        background: #f8fafc;
        /* Warna background ini penting biar data di bawahnya nggak tembus */
        color: #1e293b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
        border-bottom: 2px solid #cbd5e1;
        white-space: nowrap;
        vertical-align: middle;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        /* Shadow tipis untuk pemisah saat di-scroll */
    }

    .table-modern td {
        vertical-align: middle;
        padding: 14px 20px;
        color: #334155;
        border: 1px solid #e2e8f0;
        /* Menambah Border */
        white-space: nowrap;
    }

    /* Zebra stripe tipis agar mudah dibaca */
    .table-modern tbody tr:nth-of-type(odd) {
        background-color: #fafbfd;
    }

    .table-modern tbody tr {
        transition: background-color 0.2s;
    }

    .table-modern tbody tr:hover {
        background: #f1f5f9;
    }

    /* Highlight Warna Baris Tabel Siswa */
    .table-modern tbody tr.row-lunas {
        background-color: #dcfce7 !important;
        /* Hijau muda */
    }

    .table-modern tbody tr.row-lunas:hover {
        background-color: #bbf7d0 !important;
    }

    .table-modern tbody tr.row-belum-bayar {
        background-color: #fee2e2 !important;
        /* Merah muda */
    }

    .table-modern tbody tr.row-belum-bayar:hover {
        background-color: #fecaca !important;
    }

    .table-modern tbody tr.row-sebagian {
        background-color: #fef9c3 !important;
        /* Kuning muda (untuk bayar setengah) */
    }

    .table-modern tbody tr.row-sebagian:hover {
        background-color: #fef08a !important;
    }

    .table-modern tbody tr.spp-row-select.is-clickable {
        cursor: pointer;
    }

    .table-modern tbody tr.spp-row-select.is-clickable:hover {
        box-shadow: inset 0 0 0 2px rgba(13, 110, 253, 0.18);
    }

    .table-modern tbody tr.spp-row-selected {
        box-shadow: inset 0 0 0 2px #0d6efd !important;
    }

    /* Badges */
    .badge-soft-success,
    .badge-soft-warning,
    .badge-soft-danger,
    .badge-soft-primary {
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-block;
        font-size: 12px;
        letter-spacing: 0.3px;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-soft-warning {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .badge-soft-primary {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    /* Form Elements */
    .form-control,
    .form-select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .sticky-form {
        position: sticky;
        top: 24px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .student-mini {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .form-summary-box {
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 16px;
        height: 100%;
    }

    .form-summary-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 6px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .form-summary-value {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .table-action-btn {
        min-width: 90px;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .table-responsive {
        max-height: 550px;
        /* Batas tinggi tabel, silakan ubah angkanya sesuai selera */
        overflow-y: auto;
        /* Menambahkan scroll vertikal otomatis */
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 0 0 16px 16px;
    }

    /* Nav Tabs Styling */
    .nav-pills .nav-link {
        border-radius: 10px;
        color: #475569;
        transition: all 0.2s;
        margin-right: 8px;
        background: #fff;
        border: 1px solid #e2e8f0;
        cursor: pointer;
    }

    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.2);
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8fafc;
        color: #0f172a;
    }

    /* Tab Pane Transition fix */
    .tab-pane {
        display: none;
    }

    .tab-pane.active.show {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }



    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="page-header-spp">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <h3><i class="fa-solid fa-wallet me-3 text-primary bg-white rounded-circle p-2" style="font-size: 1.2rem;"></i> Input Pembayaran SPP Siswa</h3>
                <div class="d-flex flex-wrap mt-3">
                    <?php if (! empty($activeYear)): ?>
                        <span class="info-chip">
                            <i class="fa-solid fa-calendar-day me-2"></i> Tahun Ajaran Aktif:
                            <strong class="ms-1"><?= esc($activeYear['nama_tahun_ajaran']) ?></strong>
                        </span>
                        <span class="info-chip">
                            <i class="fa-solid fa-money-bill-wave me-2"></i> Nominal / Periode:
                            <strong class="ms-1"><?= $rupiah($activeYear['nominal_spp'] ?? 0) ?></strong>
                        </span>
                        <span class="info-chip">
                            <i class="fa-solid fa-calendar-check me-2"></i> Periode Dipilih:
                            <strong class="ms-1"><?= esc($periodeLabel) ?></strong>
                        </span>
                    <?php else: ?>
                        <span class="info-chip"><i class="fa-solid fa-triangle-exclamation me-2"></i> Tahun ajaran aktif belum tersedia</span>
                    <?php endif; ?>
                    <div class="info-chip"><i class="fa-solid fa-chalkboard-user me-2"></i> Kelas Diampu: <strong class="ms-1"><?= count($kelasGuru ?? []) ?></strong></div>
                    <div class="info-chip"><i class="fa-solid fa-users me-2"></i> Siswa Tampil: <strong class="ms-1"><?= count($siswaList ?? []) ?></strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-label"><i class="fa-solid fa-address-book me-1"></i> Total Siswa</div>
                <div class="metric-value"><?= number_format((int) ($totalSiswa ?? 0)) ?></div>
                <div class="metric-sub">Siswa aktif di periode <?= esc($periodeLabel) ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="metric-card border-bottom border-danger border-3">
                <div class="metric-label text-danger"><i class="fa-solid fa-circle-exclamation me-1"></i> Belum Lunas</div>
                <div class="metric-value text-danger"><?= number_format((int) ($totalBelumLunas ?? 0)) ?></div>
                <div class="metric-sub">Termasuk status belum bayar & sebagian</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="metric-card border-bottom border-primary border-3">
                <div class="metric-label text-primary"><i class="fa-solid fa-piggy-bank me-1"></i> Saldo SPP Periode</div>
                <div class="metric-value text-primary"><?= $rupiah($saldoSppBulanan['saldo_akhir'] ?? 0) ?></div>
                <div class="metric-sub">Pemasukan SPP periode <?= esc($periodeLabel) ?></div>
            </div>
        </div>
    </div>

    <form method="get" action="<?= site_url('guru/spp') ?>" class="toolbar-card" id="filterPeriodeForm">
        <div class="mb-4">
            <label class="form-label mb-3"><i class="fa-solid fa-list-ol me-1"></i> Progress Periode Pembayaran</label>
            <div class="timeline-stepper">
                <?php
                $stepCount = 1;
                foreach (($periodeList ?? []) as $periode):
                    $isActive = ($selectedPeriodeKey ?? '') === $periode['key'];
                ?>
                    <div class="timeline-step <?= $isActive ? 'active' : '' ?>"
                        data-bulan="<?= esc($periode['bulan']) ?>"
                        data-tahun="<?= esc($periode['tahun']) ?>">
                        <div class="step-circle"><?= $stepCount++ ?></div>
                        <div class="step-label"><?= esc($periode['label']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="bulan" id="filter_bulan" value="<?= esc($selectedBulan ?? '') ?>">
            <input type="hidden" name="tahun" id="filter_tahun" value="<?= esc($selectedTahun ?? '') ?>">
        </div>

        <hr class="text-muted mb-4">

        <div class="row g-3 align-items-end">

            <div class="col-md-9">
                <label class="form-label"><i class="fa-solid fa-magnifying-glass me-1"></i> Cari Siswa / NIS / Orang Tua</label>
                <input type="text"
                    name="search"
                    class="form-control"
                    value="<?= esc($search ?? '') ?>"
                    placeholder="Contoh: Ayu / SIS001 / Nama orang tua">
            </div>

            <div class="col-md-3">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-4 fw-medium"><i class="fa-solid fa-circle-check me-1"></i> Cari</button>
                    <a href="<?= site_url('guru/spp?bulan=' . urlencode((string) ($selectedBulan ?? '')) . '&tahun=' . urlencode((string) ($selectedTahun ?? ''))) ?>" class="btn btn-light border px-4 fw-medium">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <ul class="nav nav-pills mb-4" id="sppTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-2" id="pembayaran-tab" data-bs-toggle="tab" data-bs-target="#pembayaran-pane" type="button" role="tab" aria-controls="pembayaran-pane" aria-selected="true">
                <i class="fa-solid fa-wallet me-2"></i>Input & Daftar Siswa
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pane" type="button" role="tab" aria-controls="riwayat-pane" aria-selected="false">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>Riwayat & Ringkasan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="sppTabContent">

        <div class="tab-pane active show" id="pembayaran-pane" role="tabpanel" aria-labelledby="pembayaran-tab" tabindex="0">
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="card soft-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-list me-2"></i>Daftar Siswa</span>

                            <div class="d-flex align-items-center">
                                <div class="btn-group me-3" role="group" aria-label="Zoom Tabel">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomTable('out')" title="Perkecil Tabel"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomTable('reset')" title="Ukuran Normal"><i class="fa-solid fa-rotate-left"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomTable('in')" title="Perbesar Tabel"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                                </div>
                                <span class="badge-soft-primary fw-normal d-none d-md-inline-block"><i class="fa-solid fa-circle-info me-1"></i>Pilih siswa di bawah</span>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-modern align-middle mb-0" id="datatableSiswa">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th><i class="fa-solid fa-id-badge me-1"></i> Profil Siswa</th>
                                            <th><i class="fa-solid fa-chalkboard me-1"></i> Kelas</th>
                                            <th class="text-end"><i class="fa-solid fa-tag me-1"></i> Nominal</th>
                                            <th class="text-end"><i class="fa-solid fa-check-double me-1"></i> Terbayar</th>
                                            <th class="text-end"><i class="fa-solid fa-calculator me-1"></i> Sisa</th>
                                            <th class="text-center"><i class="fa-solid fa-shield-halved me-1"></i> Status</th>
                                            <!-- <th class="text-center" style="width: 110px;"><i class="fa-solid fa-gear me-1"></i> Aksi</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (! empty($siswaList)): ?>
                                            <?php foreach ($siswaList as $i => $row): ?>
                                                <?php
                                                $status = $row['status_pembayaran_final'] ?? 'belum_bayar';
                                                $sisa = (float) ($row['sisa_tagihan'] ?? 0);

                                                $badgeClass = match ($status) {
                                                    'lunas'    => 'badge-soft-success',
                                                    'sebagian' => 'badge-soft-warning',
                                                    default    => 'badge-soft-danger',
                                                };

                                                // Menentukan class highlight untuk baris <tr>
                                                $rowHighlightClass = match ($status) {
                                                    'lunas'    => 'row-lunas',
                                                    'sebagian' => 'row-sebagian',
                                                    default    => 'row-belum-bayar',
                                                };
                                                ?>
                                                <tr class="<?= $rowHighlightClass ?> spp-row-select <?= $sisa <= 0 ? '' : 'is-clickable' ?>"
                                                    data-id="<?= esc($row['id']) ?>"
                                                    data-disabled="<?= $sisa <= 0 ? '1' : '0' ?>">
                                                    <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                                                    <td>
                                                        <div class="fw-bold text-dark"><?= esc($row['nama_siswa']) ?></div>
                                                        <div class="student-mini">
                                                            <?= esc($row['nis']) ?>
                                                            <?php if (! empty($row['nama_orang_tua'])): ?>
                                                                • Ortu: <?= esc($row['nama_orang_tua']) ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-light text-dark border"> <?= esc($row['nama_kelas'] ?? '-') ?></span></td>
                                                    <td class="text-end fw-medium"><?= $rupiah($row['nominal_tagihan_final'] ?? 0) ?></td>
                                                    <td class="text-end fw-medium text-success"><?= $rupiah($row['nominal_terbayar_final'] ?? 0) ?></td>
                                                    <td class="text-end fw-bold <?= $sisa > 0 ? 'text-danger' : '' ?>">
                                                        <?= $rupiah($sisa) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="<?= $badgeClass ?>">
                                                            <?= ucwords(str_replace('_', ' ', $status)) ?>
                                                        </span>
                                                    </td>
                                                    
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8">
                                                    <div class="empty-state">
                                                        <i class="fa-solid fa-box-open text-muted" style="font-size: 3rem;"></i>
                                                        <h5 class="mt-3 mb-2 text-dark">Data Siswa Kosong</h5>
                                                        <div class="text-muted">Cobalah untuk mereset kata kunci pencarian atau ganti filter.</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="sticky-form">
                        <div class="card soft-card border-primary">
                            <div class="card-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                                <i class="fa-regular fa-credit-card me-2"></i> Form Input Pembayaran
                            </div>

                            <div class="card-body p-4">
                                <form action="<?= site_url('guru/spp/store') ?>" method="post" id="formPembayaranSpp">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="bulan_tagihan" value="<?= esc($selectedBulan ?? '') ?>">
                                    <input type="hidden" name="tahun_tagihan" value="<?= esc($selectedTahun ?? '') ?>">
                                    <input type="hidden" name="kelas_tahun_ajaran_filter_id" value="<?= esc($selectedKelasTahunAjaranId ?? '') ?>">

                                    <div class="mb-4">
                                        <label class="form-label text-muted">Periode Tagihan</label>
                                        <input type="text" class="form-control bg-light" value="<?= esc($periodeLabel) ?>" readonly>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label text-primary fw-bold"><i class="fa-solid fa-user-check me-1"></i> Pilih Siswa</label>
                                        <select name="siswa_id" id="siswa_id" class="form-select border-primary" required>
                                            <option value="">-- Pilih siswa dari daftar --</option>
                                            <?php foreach (($siswaList ?? []) as $row): ?>
                                                <option value="<?= esc($row['id']) ?>"
                                                    data-nama="<?= esc($row['nama_siswa'], 'attr') ?>"
                                                    data-nis="<?= esc($row['nis'], 'attr') ?>"
                                                    data-kelas="<?= esc($row['nama_kelas'] ?? '-', 'attr') ?>"
                                                    data-status="<?= esc($row['status_pembayaran_final'] ?? 'belum_bayar', 'attr') ?>"
                                                    data-nominal="<?= esc($row['nominal_tagihan_final'] ?? 0, 'attr') ?>"
                                                    data-terbayar="<?= esc($row['nominal_terbayar_final'] ?? 0, 'attr') ?>"
                                                    data-sisa="<?= esc($row['sisa_tagihan'] ?? 0, 'attr') ?>"
                                                    data-jumlah-default="<?= esc($row['jumlah_bayar_default'] ?? 0, 'attr') ?>"
                                                    <?= old('siswa_id') == $row['id'] ? 'selected' : '' ?>>
                                                    <?= esc($row['nama_siswa']) ?> - <?= esc($row['nis']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <div class="form-summary-box text-center pb-2 border-primary">
                                                <div class="form-summary-label">Nama Siswa</div>
                                                <div class="form-summary-value text-primary fs-5" id="summary_nama">-</div>
                                                <div class="text-muted small mt-1"><span id="summary_nis">-</span> • Kelas <span id="summary_kelas">-</span></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-summary-box">
                                                <div class="form-summary-label">Total Tagihan</div>
                                                <div class="form-summary-value" id="summary_nominal">Rp 0</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><i class="fa-solid fa-calendar-days me-1"></i> Tanggal Bayar</label>
                                        <input type="datetime-local"
                                            name="tanggal_bayar"
                                            class="form-control"
                                            value="<?= old('tanggal_bayar', date('Y-m-d\TH:i')) ?>"
                                            required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><i class="fa-solid fa-coins me-1"></i> Jumlah Bayar</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-end-0 bg-white fw-bold text-success">Rp</span>
                                            <input type="number"
                                                name="jumlah_bayar"
                                                id="jumlah_bayar"
                                                class="form-control border-start-0 ps-0 fw-bold text-dark fs-5"
                                                min="0"
                                                step="0.01"
                                                value="<?= old('jumlah_bayar') ?>"
                                                readonly
                                                required>
                                        </div>
                                        <div class="text-muted small mt-2"><i class="fa-solid fa-circle-info"></i> *Nominal otomatis mengikuti sisa tagihan.</div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><i class="fa-solid fa-wallet me-1"></i> Metode Pembayaran</label>
                                        <select name="metode_pembayaran" class="form-select" required>
                                            <option value="tunai" <?= old('metode_pembayaran', 'tunai') === 'tunai' ? 'selected' : '' ?>>Tunai</option>
                                            <option value="transfer" <?= old('metode_pembayaran') === 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                                            <option value="qris" <?= old('metode_pembayaran') === 'qris' ? 'selected' : '' ?>>QRIS</option>
                                            <option value="lainnya" <?= old('metode_pembayaran') === 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><i class="fa-solid fa-comment-dots me-1"></i> Keterangan (Opsional)</label>
                                        <textarea name="keterangan" rows="2" class="form-control" placeholder="Tambahkan catatan jika perlu..."><?= old('keterangan') ?></textarea>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary py-3 fw-bold fs-6 shadow-sm" id="btnSubmitPembayaran">
                                            <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Pembayaran
                                        </button>
                                        <button type="button" class="btn btn-light border py-2 fw-bold" id="btnResetForm">
                                            <i class="fa-solid fa-rotate-right me-1"></i> Reset Form
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="riwayat-pane" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
            <div class="row g-4">
                <div class="col-xl-12">
                    <div class="card soft-card">
                        <div class="card-header">
                            <i class="fa-solid fa-chart-pie me-2"></i> Ringkasan Periode <?= esc($periodeLabel) ?>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium"><i class="fa-solid fa-file-invoice-dollar me-1"></i> Total Tagihan</span>
                                <strong class="fs-6"><?= $rupiah($totalTagihan ?? 0) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium"><i class="fa-solid fa-check-double me-1"></i> Total Terbayar</span>
                                <strong class="text-success fs-6"><?= $rupiah($totalTerbayar ?? 0) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium"><i class="fa-solid fa-hourglass-half me-1"></i> Total Sisa</span>
                                <strong class="text-danger fs-6"><?= $rupiah($totalSisa ?? 0) ?></strong>
                            </div>
                            <hr class="text-light">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium">Status Lunas</span>
                                <span class="badge-soft-success"><?= number_format((int) ($totalLunas ?? 0)) ?> Siswa</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium">Status Sebagian</span>
                                <span class="badge-soft-warning"><?= number_format((int) ($totalSebagian ?? 0)) ?> Siswa</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium">Belum Lunas</span>
                                <span class="badge-soft-danger"><?= number_format((int) ($totalBelumLunas ?? 0)) ?> Siswa</span>
                            </div>
                            <div class="mt-4 p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted fw-bold text-uppercase">Pemasukan Bulan Ini</span>
                                    <strong class="text-primary"><?= $rupiah($pembayaranPeriodeGuru ?? 0) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted fw-bold text-uppercase">Total Saldo Akhir</span>
                                    <strong class="text-primary fs-5"><?= $rupiah($saldoSppBulanan['saldo_akhir'] ?? 0) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card soft-card">
                        <div class="card-header">
                            <i class="fa-solid fa-receipt me-2"></i> Riwayat Pembayaran Periode Ini
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th><i class="fa-solid fa-barcode me-1"></i> Kode Transaksi</th>
                                            <th><i class="fa-regular fa-calendar me-1"></i> Tanggal</th>
                                            <th><i class="fa-regular fa-user me-1"></i> Siswa</th>
                                            <th><i class="fa-regular fa-credit-card me-1"></i> Metode</th>
                                            <th class="text-end"><i class="fa-solid fa-money-bill me-1"></i> Total Nominal</th>
                                            <th><i class="fa-regular fa-credit-card me-1 text-center"></i> Status</th>
                                            <th class="text-center" style="min-width: 220px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (! empty($riwayatPembayaran)): ?>
                                            <?php foreach ($riwayatPembayaran as $row): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-light text-primary border px-2 py-1">
                                                            <?= esc($row['kode_pembayaran']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-muted"><?= esc(date('d M Y, H:i', strtotime($row['tanggal_bayar']))) ?></td>
                                                    <td>
                                                        <div class="fw-bold text-dark"><?= esc($row['nama_siswa']) ?></div>
                                                        <div class="student-mini"><?= esc($row['nama_kelas'] ?? '-') ?></div>
                                                    </td>
                                                    <td><span class="badge-soft-primary"><?= strtoupper(esc($row['metode_pembayaran'])) ?></span></td>
                                                      <td class="text-end fw-bold text-success"><?= $rupiah($row['jumlah_bayar']) ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                        $waStatus = $row['wa_notif_status'] ?? 'belum_dikirim';
                                                        if (empty($row['nomor_hp_orang_tua'])) {
                                                            $waStatus = 'no_wa';
                                                        }

                                                        $waBadgeClass = match ($waStatus) {
                                                            'terkirim' => 'badge-soft-success',
                                                            'dibuka' => 'badge-soft-warning',
                                                            'no_wa' => 'badge bg-secondary text-white',
                                                            default => 'badge-soft-danger',
                                                        };

                                                        $waStatusLabel = match ($waStatus) {
                                                            'terkirim' => 'Terkirim',
                                                            'dibuka' => 'Dibuka',
                                                            'no_wa' => 'No WA',
                                                            default => 'Belum Dikirim',
                                                        };
                                                        ?>
                                                        <span class="<?= $waBadgeClass ?>"><?= esc($waStatusLabel) ?></span>
                                                    </td>
                                                  
                                                    <td class="text-center">
                                                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                                                            <?php if (! empty($row['nomor_hp_orang_tua'])): ?>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success btn-send-wa"
                                                                    data-send-url="<?= site_url('guru/spp/send-whatsapp/' . $row['id']) ?>"
                                                                    data-confirm-url="<?= site_url('guru/spp/confirm-whatsapp-sent/' . $row['id']) ?>"
                                                                    data-status="<?= esc($row['wa_notif_status'] ?? 'belum_dikirim') ?>"
                                                                    data-siswa="<?= esc($row['nama_siswa']) ?>">
                                                                    <i class="fa-brands fa-whatsapp me-1"></i> <?= (($row['wa_notif_status'] ?? '') === 'terkirim') ? 'Kirim Ulang WA' : 'Kirim WA' ?>
                                                                </button>
                                                            <?php else: ?>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-secondary"
                                                                    disabled
                                                                    title="Nomor WhatsApp orang tua belum tersedia">
                                                                    <i class="fa-brands fa-whatsapp me-1"></i> No WA
                                                                </button>
                                                            <?php endif; ?>

                                                            <button type="button"
                                                                class="btn btn-sm btn-danger btn-hapus-pembayaran"
                                                                data-id="<?= esc($row['id']) ?>"
                                                                data-kode="<?= esc($row['kode_pembayaran']) ?>"
                                                                data-siswa="<?= esc($row['nama_siswa']) ?>"
                                                                data-jumlah="<?= esc($row['jumlah_bayar']) ?>">
                                                                <i class="fa-solid fa-rotate-left me-1"></i> Batalkan Transaksi
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7">
                                                    <div class="empty-state py-5">
                                                        <i class="fa-solid fa-clock-rotate-left text-muted" style="font-size: 3rem;"></i>
                                                        <div class="text-muted mt-3">Belum ada transaksi pembayaran yang tercatat pada periode ini.</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function rupiah(angka) {
        const number = parseFloat(angka || 0);
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    // --- Fitur Zoom Table Menggunakan JavaScript ---
    let currentZoomLevel = 1;

    function zoomTable(action) {
        const table = document.getElementById('datatableSiswa');

        if (action === 'in' && currentZoomLevel < 1.5) {
            currentZoomLevel += 0.1;
        } else if (action === 'out' && currentZoomLevel > 0.3) {
            currentZoomLevel -= 0.1;
        } else if (action === 'reset') {
            currentZoomLevel = 1;
        }

        table.style.fontSize = currentZoomLevel + 'em';
    }

    (function() {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?= addslashes(session()->getFlashdata('success')) ?>',
                icon: 'success',
                confirmButtonColor: '#0d6efd'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                title: 'Gagal!',
                text: '<?= addslashes(session()->getFlashdata('error')) ?>',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        <?php endif; ?>

        const formSpp = document.getElementById('formPembayaranSpp');
        if (formSpp) {
            formSpp.addEventListener('submit', function(e) {
                e.preventDefault();
                const namaSiswa = document.getElementById('summary_nama').innerText || 'Siswa ini';
                const nominal = document.getElementById('jumlah_bayar').value || 0;

                Swal.fire({
                    title: 'Konfirmasi Pembayaran',
                    html: `Apakah Anda yakin ingin menyimpan pembayaran untuk <b>${namaSiswa}</b> sebesar <b>${rupiah(nominal)}</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: '<i class="fa-solid fa-circle-check"></i> Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formSpp.submit();
                    }
                });
            });
        }

        const timelineSteps = document.querySelectorAll('.timeline-step');
        const filterBulan = document.getElementById('filter_bulan');
        const filterTahun = document.getElementById('filter_tahun');
        const filterForm = document.getElementById('filterPeriodeForm');

        timelineSteps.forEach(step => {
            step.addEventListener('click', function() {
                filterBulan.value = this.getAttribute('data-bulan') || '';
                filterTahun.value = this.getAttribute('data-tahun') || '';
                filterForm.submit();
            });
        });

        const siswaSelect = document.getElementById('siswa_id');
        const inputJumlah = document.getElementById('jumlah_bayar');
        const btnSubmit = document.getElementById('btnSubmitPembayaran');
        const btnReset = document.getElementById('btnResetForm');
        const pilihButtons = document.querySelectorAll('.btn-pilih-siswa');
        const siswaRows = document.querySelectorAll('.spp-row-select');

        const summaryNama = document.getElementById('summary_nama');
        const summaryNis = document.getElementById('summary_nis');
        const summaryKelas = document.getElementById('summary_kelas');
        const summaryStatus = document.getElementById('summary_status');
        const summaryNominal = document.getElementById('summary_nominal');

        const summaryTerbayar = document.getElementById('summary_terbayar');
        const summarySisa = document.getElementById('summary_sisa');

        function statusText(status) {
            if (status === 'lunas') return '<span class="badge-soft-success"><i class="fa-solid fa-circle-check me-1"></i> Lunas</span>';
            if (status === 'sebagian') return '<span class="badge-soft-warning"><i class="fa-solid fa-circle-minus me-1"></i> Sebagian</span>';
            return '<span class="badge-soft-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Belum Bayar</span>';
        }

        function clearFormSummary() {
            if (summaryNama) summaryNama.textContent = '-';
            if (summaryNis) summaryNis.textContent = '-';
            if (summaryKelas) summaryKelas.textContent = '-';
            if (summaryStatus) summaryStatus.innerHTML = '-';
            if (summaryNominal) summaryNominal.textContent = 'Rp 0';

            if (summaryTerbayar) summaryTerbayar.textContent = 'Rp 0';
            if (summarySisa) summarySisa.textContent = 'Rp 0';

            if (inputJumlah) inputJumlah.value = '';
            if (btnSubmit) btnSubmit.disabled = true;
        }

        function setSelectedRow(id) {
            siswaRows.forEach(function(row) {
                row.classList.remove('spp-row-selected');
                if ((row.getAttribute('data-id') || '') === String(id || '')) {
                    row.classList.add('spp-row-selected');
                }
            });
        }

        function selectSiswaById(id) {
            if (!siswaSelect || !id) return;

            for (let i = 0; i < siswaSelect.options.length; i++) {
                if (siswaSelect.options[i].value === id) {
                    siswaSelect.selectedIndex = i;
                    fillFromOption(siswaSelect.options[i]);
                    setSelectedRow(id);
                    break;
                }
            }
        }

        function fillFromOption(option) {
            if (!option || !option.value) {
                clearFormSummary();
                return;
            }

            const nama = option.getAttribute('data-nama') || '-';
            const nis = option.getAttribute('data-nis') || '-';
            const kelas = option.getAttribute('data-kelas') || '-';
            const status = option.getAttribute('data-status') || 'belum_bayar';
            const nominal = parseFloat(option.getAttribute('data-nominal') || '0');
            const terbayar = parseFloat(option.getAttribute('data-terbayar') || '0');
            const sisa = parseFloat(option.getAttribute('data-sisa') || '0');
            const jumlahDefault = parseFloat(option.getAttribute('data-jumlah-default') || '0');

            if (summaryNama) summaryNama.textContent = nama;
            if (summaryNis) summaryNis.textContent = nis;
            if (summaryKelas) summaryKelas.textContent = kelas;
            if (summaryStatus) summaryStatus.innerHTML = statusText(status);
            if (summaryNominal) summaryNominal.textContent = rupiah(nominal);

            if (summaryTerbayar) summaryTerbayar.textContent = rupiah(terbayar);
            if (summarySisa) summarySisa.textContent = rupiah(sisa);

            if (inputJumlah) inputJumlah.value = jumlahDefault > 0 ? jumlahDefault : 0;
            if (btnSubmit) btnSubmit.disabled = sisa <= 0;
        }

        if (siswaSelect) {
            siswaSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                fillFromOption(selectedOption);
                setSelectedRow(this.value || '');
            });
        }

        pilihButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const id = this.getAttribute('data-id') || '';
                selectSiswaById(id);
            });
        });

        siswaRows.forEach(function(row) {
            row.addEventListener('click', function(e) {
                if (e.target.closest('.btn-pilih-siswa')) return;

                const disabled = this.getAttribute('data-disabled') === '1';
                if (disabled) return;

                const id = this.getAttribute('data-id') || '';
                selectSiswaById(id);
            });
        });

        if (btnReset) {
            btnReset.addEventListener('click', function() {
                siswaSelect.value = '';
                clearFormSummary();
                setSelectedRow('');
            });
        }

        if (siswaSelect && siswaSelect.value) {
            fillFromOption(siswaSelect.options[siswaSelect.selectedIndex]);
            setSelectedRow(siswaSelect.value);
        } else {
            clearFormSummary();
            setSelectedRow('');
        }

        const tabButtons = document.querySelectorAll('#sppTab .nav-link');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('show', 'active'));

                this.classList.add('active');

                const targetId = this.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });
    })();
</script>

<script>
    document.querySelectorAll('.btn-send-wa').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const sendUrl = this.getAttribute('data-send-url');
            const confirmUrl = this.getAttribute('data-confirm-url');
            const siswa = this.getAttribute('data-siswa') || 'siswa';
            const status = this.getAttribute('data-status') || 'belum_dikirim';

            window.open(sendUrl, '_blank', 'noopener');

            Swal.fire({
                title: status === 'terkirim' ? 'Kirim ulang notifikasi?' : 'WhatsApp dibuka',
                html: `Silakan kirim pesan WhatsApp untuk <b>${siswa}</b>.<br><br>Setelah pesan benar-benar terkirim, klik <b>Sudah Terkirim</b>.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-check"></i> Sudah Terkirim',
                cancelButtonText: 'Nanti Saja'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = confirmUrl;
                }
            });
        });
    });
</script>

<script>
    document.querySelectorAll('.btn-hapus-pembayaran').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const kode = this.getAttribute('data-kode');
            const siswa = this.getAttribute('data-siswa');
            const jumlah = this.getAttribute('data-jumlah');

            Swal.fire({
                title: 'Batalkan pembayaran?',
                html: `
                <div class="text-start">
                    <div><strong>Kode:</strong> ${kode}</div>
                    <div><strong>Siswa:</strong> ${siswa}</div>
                    <div><strong>Nominal:</strong> ${rupiah(jumlah)}</div>
                    <div class="mt-3 text-danger">
                        Status siswa ini akan dikembalikan otomatis.
                    </div>
                </div>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?= site_url('guru/spp/delete') ?>/' + id;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '<?= csrf_token() ?>';
                    csrfInput.value = '<?= csrf_hash() ?>';
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>