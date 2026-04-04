<?= $this->extend('admin/template/layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">

<style>
    .card-modern {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .card-header-custom {
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }

    .btn-modern {
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-modern:hover {
        transform: scale(1.05);
    }

    .border-edit {
        border: 2px solid #ffc107 !important;
    }

    .bg-edit {
        background: linear-gradient(45deg, #ffc107, #ffca2c);
        color: #000;
    }

    .bg-add {
        background: linear-gradient(45deg, #0d6efd, #0b5ed7);
        color: #fff;
    }

    .custom-tabs .nav-link {
        border-radius: 30px;
        padding: 10px 24px;
        font-weight: 600;
        color: #6c757d;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        margin-right: 10px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        cursor: pointer;
    }

    .custom-tabs .nav-link:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
    }

    .custom-tabs .nav-link.active {
        background: linear-gradient(45deg, #0d6efd, #0b5ed7);
        color: #fff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        border-color: transparent;
    }

    .tab-pane {
        transition: opacity 0.3s ease;
    }

    .form-floating>.form-control,
    .form-floating>.flatpickr-input {
        border-radius: 10px;
        border: 1px solid #ced4da;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .form-floating>.form-control:focus,
    .form-floating>.flatpickr-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    .form-floating>label {
        padding-left: 1rem;
    }

    .form-floating-date {
        position: relative;
    }

    .form-floating-date input[type="text"] {
        color: #495057;
        cursor: pointer;
        padding-right: 2.5rem;
        background-color: #fff !important;
    }

    .form-floating-date .date-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d6efd;
        font-size: 1.2rem;
        z-index: 10;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .form-floating-date input:focus~.date-icon,
    .form-floating-date input:hover~.date-icon {
        color: #0b5ed7;
    }
</style>

<?php
$request = service('request');

$requestedTab = strtolower((string) ($request->getGet('tab') ?? 'kelas'));
$activeTab = in_array($requestedTab, ['kelas', 'tahun'], true) ? $requestedTab : 'kelas';

if ($tahunEdit || $request->getGet('edit_tahun')) {
    $activeTab = 'tahun';
} elseif ($kelasEdit || $request->getGet('edit_kelas')) {
    $activeTab = 'kelas';
}
?>

<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h3 class="mb-0 text-gray-800 fw-bold">Manajemen Kelas & Tahun Ajaran</h3>
    </div>

    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger rounded-3 shadow-sm animate__animated animate__shakeX">
            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat Kesalahan:</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <ul class="nav nav-pills mb-4 custom-tabs" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-trigger <?= $activeTab == 'kelas' ? 'active' : '' ?>" id="pills-kelas-tab" data-tab="kelas" data-target="#pills-kelas" type="button" role="tab">
                <i class="fas fa-chalkboard-teacher me-2"></i>Data Kelas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-trigger <?= $activeTab == 'tahun' ? 'active' : '' ?>" id="pills-tahun-tab" data-tab="tahun" data-target="#pills-tahun" type="button" role="tab">
                <i class="fas fa-calendar-alt me-2"></i>Tahun Ajaran
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">

        <div class="tab-pane fade <?= $activeTab == 'kelas' ? 'show active' : '' ?>" id="pills-kelas" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card card-modern <?= $kelasEdit ? 'border-edit' : '' ?>">
                        <div class="card-header card-header-custom <?= $kelasEdit ? 'bg-edit' : 'bg-add' ?> py-3">
                            <i class="fas <?= $kelasEdit ? 'fa-edit' : 'fa-plus-circle' ?> me-2"></i>
                            <?= $kelasEdit ? 'Edit Kelas' : 'Tambah Kelas Baru' ?>
                            <?php if ($kelasEdit): ?>
                                <span class="badge bg-dark float-end">Mode Edit</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="<?= $kelasEdit ? site_url('admin/manage-class/kelas/update/' . $kelasEdit['id']) : site_url('admin/manage-class/kelas/store') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="current_tab" value="kelas">

                                <div class="form-floating mb-4">
                                    <input type="text" name="nama_kelas" class="form-control" id="namaKelas"
                                        value="<?= old('nama_kelas', $kelasEdit['nama_kelas'] ?? '') ?>"
                                        placeholder="Contoh: TK A" required>
                                    <label for="namaKelas">Nama Kelas</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <textarea name="deskripsi" class="form-control" id="deskripsiKelas" style="height: 120px" placeholder="Deskripsi kelas"><?= old('deskripsi', $kelasEdit['deskripsi'] ?? '') ?></textarea>
                                    <label for="deskripsiKelas">Deskripsi</label>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-<?= $kelasEdit ? 'warning' : 'primary' ?> btn-modern py-2 fw-bold">
                                        <i class="fas fa-save me-1"></i> <?= $kelasEdit ? 'Update Kelas' : 'Simpan Kelas' ?>
                                    </button>
                                    <?php if ($kelasEdit): ?>
                                        <a href="<?= site_url('admin/manage-class?tab=kelas') ?>" class="btn btn-secondary btn-modern py-2">
                                            <i class="fas fa-times me-1"></i> Batal
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-modern h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>Daftar Kelas</h6>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Nama Kelas</th>
                                        <th>Deskripsi</th>
                                        <th width="120" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($kelas)): ?>
                                        <?php foreach ($kelas as $i => $item): ?>
                                            <tr>
                                                <td class="text-center"><?= $i + 1 ?></td>
                                                <td class="fw-bold text-dark"><?= esc($item['nama_kelas']) ?></td>
                                                <td class="text-muted small"><?= esc($item['deskripsi']) ?></td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('admin/manage-class?tab=kelas&edit_kelas=' . $item['id']) ?>" class="btn btn-outline-warning btn-sm btn-modern" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?= site_url('admin/manage-class/kelas/delete/' . $item['id']) ?>" method="post" class="d-inline form-delete" id="form-del-kelas-<?= $item['id'] ?>">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="current_tab" value="kelas">
                                                        <button type="button" class="btn btn-outline-danger btn-sm btn-modern btn-delete" data-form="form-del-kelas-<?= $item['id'] ?>" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 d-block text-light"></i>
                                                Belum ada data kelas.
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

        <div class="tab-pane fade <?= $activeTab == 'tahun' ? 'show active' : '' ?>" id="pills-tahun" role="tabpanel">
            <div class="row g-4">

                <div class="col-12">
                    <div class="card card-modern <?= $tahunEdit ? 'border-edit' : '' ?>">
                        <div class="card-header card-header-custom <?= $tahunEdit ? 'bg-edit' : 'bg-add' ?> py-3">
                            <i class="fas <?= $tahunEdit ? 'fa-calendar-edit' : 'fa-calendar-plus' ?> me-2"></i>
                            <?= $tahunEdit ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' ?>
                            <?php if ($tahunEdit): ?>
                                <span class="badge bg-dark float-end">Mode Edit</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="<?= $tahunEdit ? site_url('admin/manage-class/tahun-ajaran/update/' . $tahunEdit['id']) : site_url('admin/manage-class/tahun-ajaran/store') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="current_tab" value="tahun">

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="nama_tahun_ajaran" class="form-control" id="namaTahun"
                                                value="<?= old('nama_tahun_ajaran', $tahunEdit['nama_tahun_ajaran'] ?? '') ?>"
                                                placeholder="Contoh: 2025/2026" required>
                                            <label for="namaTahun">Nama Tahun Ajaran</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-date">
                                            <input type="text" name="tanggal_mulai" class="form-control datepicker-modern" id="tglMulai"
                                                value="<?= old('tanggal_mulai', $tahunEdit['tanggal_mulai'] ?? '') ?>" placeholder="Pilih Tanggal Mulai" required>
                                            <label for="tglMulai">Tanggal Mulai</label>
                                            <i class="fas fa-calendar-alt date-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-date">
                                            <input type="text" name="tanggal_selesai" class="form-control datepicker-modern" id="tglSelesai"
                                                value="<?= old('tanggal_selesai', $tahunEdit['tanggal_selesai'] ?? '') ?>" placeholder="Pilih Tanggal Selesai" required>
                                            <label for="tglSelesai">Tanggal Selesai</label>
                                            <i class="fas fa-calendar-check date-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center">
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold" style="border-radius: 10px 0 0 10px; border: 1px solid #ced4da;">Rp</span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="hidden" name="nominal_spp" id="nominalSpp"
                                                    value="<?= old('nominal_spp', $tahunEdit['nominal_spp'] ?? '') ?>">

                                                <input type="text" class="form-control" id="nominalSppDisplay"
                                                    value="<?= old('nominal_spp', $tahunEdit['nominal_spp'] ?? '') !== '' ? number_format((float) old('nominal_spp', $tahunEdit['nominal_spp'] ?? ''), 0, ',', '.') : '' ?>"
                                                    placeholder="Nominal SPP"
                                                    inputmode="numeric"
                                                    autocomplete="off"
                                                    style="border-radius: 0 10px 10px 0;"
                                                    required>

                                                <label for="nominalSppDisplay">Nominal SPP</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch d-flex align-items-center justify-content-center">
                                            <input type="checkbox" name="is_active" value="1" class="form-check-input mt-0" id="is_active" role="switch"
                                                style="width: 3em; height: 1.5em; cursor: pointer;" <?= old('is_active', $tahunEdit['is_active'] ?? 0) ? 'checked' : '' ?>>
                                            <label class="form-check-label ms-3 fw-bold text-primary" for="is_active" style="cursor: pointer;">
                                                Jadikan Aktif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if ($tahunEdit): ?>
                                            <a href="<?= site_url('admin/manage-class?tab=tahun') ?>" class="btn btn-secondary btn-modern py-2 me-2">
                                                <i class="fas fa-times me-1"></i> Batal
                                            </a>
                                        <?php endif; ?>
                                        <button type="submit" class="btn btn-<?= $tahunEdit ? 'warning' : 'primary' ?> btn-modern py-2 fw-bold w-auto px-4">
                                            <i class="fas fa-save me-1"></i> <?= $tahunEdit ? 'Update' : 'Simpan Tahun Ajaran' ?>
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card card-modern h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt me-2"></i>Daftar Tahun Ajaran</h6>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Periode</th>
                                        <th>SPP</th>
                                        <th class="text-center">Status</th>
                                        <th width="120" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tahunAjaran)): ?>
                                        <?php foreach ($tahunAjaran as $i => $item): ?>
                                            <tr>
                                                <td class="text-center"><?= $i + 1 ?></td>
                                                <td class="fw-bold text-dark"><?= esc($item['nama_tahun_ajaran']) ?></td>
                                                <td class="small">
                                                    <span class="text-success"><i class="fas fa-play-circle me-1"></i> <?= date('d M Y', strtotime($item['tanggal_mulai'])) ?></span> <br>
                                                    <span class="text-danger"><i class="fas fa-stop-circle me-1"></i> <?= date('d M Y', strtotime($item['tanggal_selesai'])) ?></span>
                                                </td>
                                                <td class="fw-bold text-primary">Rp <?= number_format((float) $item['nominal_spp'], 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <?php if ((int) $item['is_active'] === 1): ?>
                                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary rounded-pill px-3 py-2"><i class="fas fa-minus-circle me-1"></i> Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('admin/manage-class?tab=tahun&edit_tahun=' . $item['id']) ?>" class="btn btn-outline-warning btn-sm btn-modern" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?= site_url('admin/manage-class/tahun-ajaran/delete/' . $item['id']) ?>" method="post" class="d-inline form-delete" id="form-del-tahun-<?= $item['id'] ?>">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="current_tab" value="tahun">
                                                        <button type="button" class="btn btn-outline-danger btn-sm btn-modern btn-delete" data-form="form-del-tahun-<?= $item['id'] ?>" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 d-block text-light"></i>
                                                Belum ada data tahun ajaran.
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".datepicker-modern", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            locale: "id",
            disableMobile: true,
            animate: true
        });

        const tabLinks = document.querySelectorAll('.tab-trigger');
        const tabPanes = document.querySelectorAll('.tab-content .tab-pane');

        function updateTabUrl(tabName) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            url.searchParams.delete('edit_kelas');
            url.searchParams.delete('edit_tahun');
            window.history.replaceState({}, '', url);
        }

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                tabLinks.forEach(t => t.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('show', 'active'));

                this.classList.add('active');

                const targetId = this.getAttribute('data-target');
                const targetPane = document.querySelector(targetId);
                const tabName = this.getAttribute('data-tab');

                if (targetPane) {
                    setTimeout(() => {
                        targetPane.classList.add('show', 'active');
                    }, 50);
                }

                if (tabName) {
                    updateTabUrl(tabName);
                }
            });
        });

        const nominalHidden = document.getElementById('nominalSpp');
        const nominalDisplay = document.getElementById('nominalSppDisplay');

        function normalizeNominalValue(value) {
            const str = String(value ?? '').trim();
            if (str === '') return '';

            // kalau dari DB bentuknya 80000.00
            if (/^\d+(\.\d{1,2})$/.test(str)) {
                return String(Math.round(parseFloat(str)));
            }

            // fallback: ambil digit saja
            return str.replace(/\D/g, '');
        }

        function formatRupiah(value) {
            const normalized = normalizeNominalValue(value);
            return normalized.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function syncHiddenFromDisplay() {
            if (!nominalHidden || !nominalDisplay) return;
            nominalHidden.value = nominalDisplay.value.replace(/\D/g, '');
        }

        function renderDisplayFromHidden() {
            if (!nominalHidden || !nominalDisplay) return;
            const normalized = normalizeNominalValue(nominalHidden.value);
            nominalHidden.value = normalized;
            nominalDisplay.value = normalized ? formatRupiah(normalized) : '';
        }

        if (nominalHidden && nominalDisplay) {
            renderDisplayFromHidden();

            nominalDisplay.addEventListener('focus', function() {
                const raw = normalizeNominalValue(nominalHidden.value || nominalDisplay.value);
                this.value = raw;
                setTimeout(() => this.select(), 0);
            });

            nominalDisplay.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                nominalHidden.value = this.value;
            });

            nominalDisplay.addEventListener('blur', function() {
                syncHiddenFromDisplay();
                renderDisplayFromHidden();
            });

            const tahunForm = nominalDisplay.closest('form');
            if (tahunForm) {
                tahunForm.addEventListener('submit', function() {
                    syncHiddenFromDisplay();
                });
            }
        }

        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('success') ?>',
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                position: 'top-end'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#0d6efd'
            });
        <?php endif; ?>

        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const formId = this.getAttribute('data-form');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                        document.getElementById(formId).submit();
                    }
                })
            });
        });
    });
</script>

<?= $this->endSection() ?>