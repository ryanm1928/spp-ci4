<?= $this->extend('admin/template/layout') ?>

<?= $this->section('content') ?>

<style>
    .hover-elevate-up {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-elevate-up:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .icon-shape {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.5rem;
    }

    .bg-light-primary {
        background-color: #e0e8f9;
        color: #4e73df;
    }

    .bg-light-success {
        background-color: #e6f4ea;
        color: #1cc88a;
    }

    .bg-light-info {
        background-color: #e3f2fd;
        color: #36b9cc;
    }

    .bg-light-warning {
        background-color: #fef5e5;
        color: #f6c23e;
    }

    .bg-light-danger {
        background-color: #fce4e4;
        color: #e74a3b;
    }

    .bg-light-secondary {
        background-color: #f8f9fc;
        color: #858796;
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(15px);
    }

    .delay-1 {
        animation-delay: 0.1s;
    }

    .delay-2 {
        animation-delay: 0.2s;
    }

    .delay-3 {
        animation-delay: 0.3s;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container-fluid fade-in-up">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-lg hover-elevate-up" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="icon-shape bg-light-primary mr-4 d-none d-sm-flex">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 font-weight-bold text-dark">Dashboard Admin</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row fade-in-up delay-1">
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-left-primary border-0 rounded-lg h-100 hover-elevate-up">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa Aktif</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($totalSiswa) ?></div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-light-primary">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-left-success border-0 rounded-lg h-100 hover-elevate-up">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Kelas</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($totalKelas) ?></div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-light-success">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-left-info border-0 rounded-lg h-100 hover-elevate-up">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tahun Ajaran Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= esc($tahunAjaranAktif->nama_tahun_ajaran ?? '-') ?>
                            </div>
                            <small class="text-muted">
                                SPP: Rp <?= number_format((float)($tahunAjaranAktif->nominal_spp ?? 0), 0, ',', '.') ?>
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-light-info">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row fade-in-up delay-2">
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0 rounded-lg h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-users-cog text-secondary mr-2"></i> Pengguna Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 hover-elevate-up p-2 rounded bg-light">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-light-danger mr-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span class="font-weight-bold">Admin</span>
                        </div>
                        <h4 class="mb-0 text-dark"><?= number_format($totalAdmin) ?></h4>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 hover-elevate-up p-2 rounded bg-light">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-light-primary mr-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <span class="font-weight-bold">Guru</span>
                        </div>
                        <h4 class="mb-0 text-dark"><?= number_format($totalGuru) ?></h4>
                    </div>
                    <div class="d-flex justify-content-between align-items-center hover-elevate-up p-2 rounded bg-light">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-light-success mr-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <span class="font-weight-bold">Kepala Sekolah</span>
                        </div>
                        <h4 class="mb-0 text-dark"><?= number_format($totalKepalaSekolah) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm border-0 rounded-lg h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-file-invoice-dollar text-secondary mr-2"></i> Status Tagihan SPP</h6>
                </div>
                <div class="card-body">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="text-center hover-elevate-up p-3 rounded">
                                <div class="icon-shape bg-light-danger mx-auto mb-3">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Belum Bayar</div>
                                <div class="h2 mb-0 font-weight-bold text-gray-800"><?= number_format($totalBelumBayar) ?></div>
                                <small class="text-muted">Tagihan</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center hover-elevate-up p-3 rounded">
                                <div class="icon-shape bg-light-success mx-auto mb-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lunas</div>
                                <div class="h2 mb-0 font-weight-bold text-gray-800"><?= number_format($totalLunas) ?></div>
                                <small class="text-muted">Tagihan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>