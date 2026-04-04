<?= $this->extend('guru/template/layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #eef2ff;
        --text-dark: #2b3445;
        --text-muted: #8392a5;
        --bg-body: #f7f9fc;
        --radius-lg: 16px;
        --radius-md: 12px;
        --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    body {
        background-color: var(--bg-body);
    }

    .modern-card {
        background: #ffffff;
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        transition: transform 0.2s ease;
    }

    .icon-box {
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-md);
        font-size: 1.5rem;
    }

    .icon-primary {
        background: var(--primary-light);
        color: var(--primary-color);
    }

    .icon-success {
        background: #e8f8f5;
        color: #20c997;
    }

    .icon-warning {
        background: #fff8e6;
        color: #f5b041;
    }

    .icon-danger {
        background: #fdeded;
        color: #e74c3c;
    }

    .filter-wrapper {
        background: #ffffff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        padding: 1rem 1.5rem;
    }

    .filter-wrapper label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 0.3rem;
    }

    .modern-select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        box-shadow: none;
        font-weight: 500;
        color: var(--text-dark);
    }

    .modern-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }

    .modern-tabs {
        border-bottom: none;
        gap: 10px;
    }

    .modern-tabs .nav-link {
        border: none !important;
        color: var(--text-muted);
        border-radius: 8px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
        background: transparent;
        outline: none;
    }

    .modern-tabs .nav-link:hover {
        background: var(--primary-light);
        color: var(--primary-color);
    }

    .modern-tabs .nav-link.active {
        background: var(--primary-color) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    .modern-table th {
        background-color: transparent;
        border-top: none;
        border-bottom: 2px solid #edf2f9;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding-bottom: 1rem;
    }

    .modern-table td {
        vertical-align: middle;
        border-top: 1px solid #edf2f9;
        color: var(--text-dark);
        padding: 1rem 0.75rem;
    }

    .badge-modern {
        padding: 0.5em 0.8em;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .tab-pane-custom {
        display: none;
    }

    .tab-pane-custom.active {
        display: block;
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold mb-1" style="color: var(--text-dark);">Overview SPP Kelas <?= esc($selectedClassName ?? '-') ?></h3>
            <p class="text-muted mb-0">
               
            
                <i class="fa-solid fa-calendar-days mr-1"></i>
                TA: <?= esc($selectedYear->nama_tahun_ajaran ?? '-') ?>
                &nbsp;|&nbsp;
                <i class="fa-solid fa-clock mr-1"></i>
                Bulan: <?= esc($monthOptions[$selectedPeriod] ?? '-') ?>
            </p>
        </div>
    </div>

    <div class="filter-wrapper mb-4">
        <form method="get" action="<?= site_url('guru/dashboard') ?>">
            <div class="row align-items-end">
                <div class="col-md-5 mb-2 mb-md-0">
                    <label>Kelas</label>
                    <input type="text" class="form-control modern-select" value="<?= esc($selectedClassName ?? '-') ?>" readonly>
                </div>

                <div class="col-md-4 mb-2 mb-md-0">
                    <label>Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control modern-select" onchange="this.form.submit()">
                        <?php foreach ($tahunAjaranList as $ta): ?>
                            <option value="<?= esc($ta->id) ?>" <?= (int) $selectedYearId === (int) $ta->id ? 'selected' : '' ?>>
                                <?= esc($ta->nama_tahun_ajaran) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                    <label>Periode Bulan</label>
                    <select name="periode_bulan" id="periode_bulan" class="form-control modern-select">
                        <?php foreach ($monthOptions as $periodKey => $periodLabel): ?>
                            <option value="<?= esc($periodKey) ?>" <?= $selectedPeriod === $periodKey ? 'selected' : '' ?>>
                                <?= esc($periodLabel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12 mt-3 d-flex justify-content-end">
                    <button type="submit" class="btn mr-2 text-white" style="background-color: var(--primary-color); border:none; border-radius: 8px; min-width: 140px;">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>
                    <a href="<?= site_url('guru/dashboard?tahun_ajaran_id=' . (int) $selectedYearId) ?>" class="btn btn-light" style="border-radius: 8px; border: 1px solid #e2e8f0;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-right text-muted"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <?php if (! $hasAssignedClass): ?>
        <div class="alert alert-warning border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>
            Guru ini belum memiliki kelas pada tahun ajaran yang dipilih.
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="modern-card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="icon-box icon-primary mr-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-muted small font-weight-bold mb-0 uppercase">Total Siswa Kelas</p>
                        <h3 class="font-weight-bold mb-0" style="color: var(--text-dark);"><?= number_format($jumlahSiswa, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="modern-card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="icon-box icon-success mr-3">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-muted small font-weight-bold mb-0 uppercase">Pemasukan SPP Kelas</p>
                        <h4 class="font-weight-bold mb-0" style="color: var(--text-dark);">Rp <?= number_format($totalPembayaran, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="modern-card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="icon-box icon-warning mr-3">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <p class="text-muted small font-weight-bold mb-0 uppercase">Sudah Bayar</p>
                        <h3 class="font-weight-bold mb-0" style="color: var(--text-dark);"><?= number_format($jumlahSiswaSudahBayar, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="modern-card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="icon-box icon-danger mr-3">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <div>
                        <p class="text-muted small font-weight-bold mb-0 uppercase">Belum Bayar</p>
                        <h3 class="font-weight-bold mb-0" style="color: var(--text-dark);"><?= number_format($jumlahSiswaBelumBayar, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modern-card p-4">
        <ul class="nav nav-tabs modern-tabs mb-4" id="dashboardTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link active"
                    id="grafik-tab"
                    data-tab-target="#grafik"
                    role="tab"
                    aria-controls="grafik"
                    aria-selected="true">
                    <i class="fa-solid fa-chart-column mr-2"></i> Grafik Pendapatan Kelas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link"
                    id="detail-tab"
                    data-tab-target="#detail"
                    role="tab"
                    aria-controls="detail"
                    aria-selected="false">
                    <i class="fa-solid fa-table-list mr-2"></i> Detail Siswa Kelas
                </button>
            </li>
        </ul>

        <div class="tab-content" id="dashboardTabContent">
            <div class="tab-pane-custom active" id="grafik" role="tabpanel" aria-labelledby="grafik-tab">
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="sppIncomeChart"></canvas>
                </div>
            </div>

            <div class="tab-pane-custom" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                <div class="table-responsive">
                    <table class="table modern-table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Siswa</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-right">Tagihan</th>
                                <th class="text-right">Terbayar</th>
                                <th class="text-center">Jatuh Tempo</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($detailSiswa)): ?>
                                <?php foreach ($detailSiswa as $i => $row): ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $i + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3"
                                                    style="width: 36px; height: 36px; background: #f1f5f9; color: var(--primary-color); font-weight: bold;">
                                                    <?= substr(esc($row->nama_siswa), 0, 1) ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold" style="color: var(--text-dark);">
                                                        <?= esc($row->nama_siswa) ?>
                                                    </h6>
                                                    <small class="text-muted">NIS: <?= esc($row->nis) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; color: var(--text-muted);">
                                                <?= esc($row->nama_kelas) ?>
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-bold" style="color: var(--text-dark);">
                                            Rp <?= number_format((float) $row->nominal_tagihan, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-success">
                                            Rp <?= number_format((float) $row->nominal_terbayar, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center" style="color: var(--text-muted); font-size: 0.9rem;">
                                            <?= !empty($row->tanggal_jatuh_tempo) ? date('d/m/Y', strtotime($row->tanggal_jatuh_tempo)) : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $status = $row->status_pembayaran ?? null;
                                            if ($status === 'lunas') {
                                                echo '<span class="badge badge-modern bg-success text-white"><i class="fa-solid fa-check mr-1"></i> Lunas</span>';
                                            } elseif ($status === 'sebagian') {
                                                echo '<span class="badge badge-modern" style="background-color: #f5b041; color: #fff;"><i class="fa-solid fa-clock-rotate-left mr-1"></i> Sebagian</span>';
                                            } elseif ($status === 'belum_bayar') {
                                                echo '<span class="badge badge-modern bg-danger text-white"><i class="fa-solid fa-xmark mr-1"></i> Belum Bayar</span>';
                                            } else {
                                                echo '<span class="badge badge-modern bg-secondary text-white">Belum Ada Tagihan</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div style="color: #cbd5e1;">
                                            <i class="fa-solid fa-box-open fa-3x mb-3"></i>
                                            <h6 class="text-muted">Tidak ada data siswa untuk kelas ini.</h6>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('#dashboardTab [data-tab-target]');
        const tabPanes = document.querySelectorAll('#dashboardTabContent .tab-pane-custom');

        function activateTab(targetSelector) {
            tabButtons.forEach(button => {
                const isActive = button.getAttribute('data-tab-target') === targetSelector;
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            tabPanes.forEach(pane => {
                const isActive = '#' + pane.id === targetSelector;
                pane.classList.toggle('active', isActive);
            });
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetSelector = this.getAttribute('data-tab-target');
                if (targetSelector) {
                    activateTab(targetSelector);
                }
            });
        });

        activateTab('#grafik');
    });

    const chartLabels = <?= json_encode($chartLabels) ?>;
    const chartData = <?= json_encode($chartData) ?>;
    const chartCanvas = document.getElementById('sppIncomeChart');

    if (chartCanvas && typeof Chart !== 'undefined') {
        const oldChart = Chart.getChart(chartCanvas);
        if (oldChart) {
            oldChart.destroy();
        }

        new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pemasukan SPP Kelas',
                    data: chartData,
                    backgroundColor: '#4361ee',
                    borderColor: '#4361ee',
                    borderWidth: 0,
                    borderRadius: 8,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2b3445',
                        titleFont: {
                            size: 13,
                            family: "'Inter', sans-serif"
                        },
                        bodyFont: {
                            size: 14,
                            family: "'Inter', sans-serif",
                            weight: 'bold'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw || 0);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#8392a5',
                            font: {
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: {
                            color: '#edf2f9',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#8392a5',
                            font: {
                                family: "'Inter', sans-serif"
                            },
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Juta';
                                if (value >= 1000) return 'Rp ' + (value / 1000) + 'k';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    }
</script>

<?= $this->endSection() ?>