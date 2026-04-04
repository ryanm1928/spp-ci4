<?= $this->extend('admin/template/layout') ?>

<?= $this->section('content') ?>

<style>
    /* Konfigurasi Dasar & Animasi */
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --bg-soft: #f8fafc;
        --border-color: #e2e8f0;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Card Utama */
    .modern-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(226, 232, 240, 0.6);
        padding: 28px;
        animation: fadeIn 0.5s ease-out;
    }

    /* Header Section */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 4px;
    }

    /* Tombol Primary (Tambah User) */
    .btn-custom-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-custom-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        color: white;
    }

    /* Toolbar & Search Area */
    .toolbar {
        margin-bottom: 24px;
        background: var(--bg-soft);
        padding: 16px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 320px;
    }

    .search-wrapper .fa-magnifying-glass {
        position: absolute;
        left: 16px;
        color: #94a3b8;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .search-input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 16px 12px 42px;
        outline: none;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        color: var(--text-dark);
        background: #fff;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.01);
    }

    .search-input::placeholder {
        color: #94a3b8;
    }

    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .search-input:focus+.fa-magnifying-glass,
    .search-wrapper:focus-within .fa-magnifying-glass {
        color: var(--primary-color);
    }

    /* Tombol Search & Reset Custom */
    .btn-search {
        background-color: var(--text-dark);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-search:hover {
        background-color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        color: #fff;
    }

    .btn-reset {
        background-color: #ffffff;
        color: var(--text-muted);
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 18px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-reset:hover {
        background-color: #f1f5f9;
        color: var(--text-dark);
        border-color: #94a3b8;
    }

    /* Zoom Controls */
    .zoom-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        padding: 6px 12px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }

    .btn-zoom {
        background: transparent;
        border: none;
        color: var(--text-dark);
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .btn-zoom:hover {
        background: var(--bg-soft);
        color: var(--primary-color);
    }

    .btn-zoom-text {
        font-weight: 600;
        font-size: 0.9rem;
        min-width: 50px;
        text-align: center;
    }

    /* Tombol Aksi */
    .btn-action-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        font-size: 1rem;
        border: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        cursor: pointer;
    }

    .btn-edit-soft {
        background: #eff6ff;
        color: #3b82f6;
    }

    .btn-edit-soft:hover {
        background: #3b82f6;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25);
    }

    .btn-delete-soft {
        background: #fef2f2;
        color: #ef4444;
    }

    .btn-delete-soft:hover {
        background: #ef4444;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
    }

    /* Tabel Modern & Horizontal Scrolling */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 10px; /* Space for scrollbar */
    }

    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        white-space: nowrap; /* Mencegah teks turun ke bawah, menyesuaikan data */
        transition: zoom 0.2s ease;
    }

    .table-modern thead th {
        background: #f1f5f9;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 14px 20px;
        border: none;
    }

    .table-modern thead th:first-child {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }

    .table-modern thead th:last-child {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .table-modern tbody tr {
        transition: all 0.2s ease;
        animation: slideUp 0.4s ease-out forwards;
        opacity: 0;
    }

    /* Animation Delays */
    .table-modern tbody tr:nth-child(1) { animation-delay: 0.05s; }
    .table-modern tbody tr:nth-child(2) { animation-delay: 0.1s; }
    .table-modern tbody tr:nth-child(3) { animation-delay: 0.15s; }
    .table-modern tbody tr:nth-child(4) { animation-delay: 0.2s; }

    .table-modern tbody tr:hover td {
        background: var(--bg-soft);
    }

    .table-modern td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        transition: background 0.2s;
    }

    /* Avatars & Badges */
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .avatar-img {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #e2e8f0;
    }

    .avatar-initial {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 1.1rem;
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #e2e8f0;
    }

    .badge-soft {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-active {
        background-color: #dcfce7;
        color: #166534;
    }

    .badge-inactive {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .badge-role {
        background-color: #e0e7ff;
        color: #3730a3;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
    }

    /* Custom SweetAlert Popup */
    .swal2-popup.swal-custom {
        border-radius: 16px;
        padding: 1.5rem;
    }
    .swal2-confirm.btn-swal-confirm { border-radius: 8px !important; }
    .swal2-cancel.btn-swal-cancel { border-radius: 8px !important; }
</style>

<div class="modern-card">
    <div class="page-header">
        <div>
            <h2 class="page-title">Manajemen User</h2>
            <p class="page-subtitle">Kelola daftar akun pengguna sistem Anda dengan mudah.</p>
        </div>

        <a href="<?= site_url('admin/users/create') ?>" class="btn-custom-primary">
            <i class="fa-solid fa-user-plus"></i> Tambah User Baru
        </a>
    </div>

    <div class="toolbar">
        <form action="<?= site_url('admin/users') ?>" method="get" class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-3">
            
            <div class="d-flex flex-wrap align-items-center gap-3 flex-grow-1">
                <div class="search-wrapper" style="max-width: 400px; flex-grow: 1;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="keyword" value="<?= esc($keyword ?? '') ?>" class="search-input" placeholder="Cari username, email, atau role...">
                </div>
                
                <button type="submit" class="btn-search">
                    Cari
                </button>

                <?php if (!empty($keyword)): ?>
                    <a href="<?= site_url('admin/users') ?>" class="btn-reset">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                <?php endif; ?>
            </div>

            <div class="zoom-controls ms-auto">
                <span class="text-muted" style="font-size: 0.85rem; font-weight: 500;">Zoom:</span>
                <button type="button" class="btn-zoom" id="btn-zoom-out" data-bs-toggle="tooltip" title="Zoom Out">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <button type="button" class="btn-zoom btn-zoom-text" id="btn-zoom-reset" data-bs-toggle="tooltip" title="Reset Zoom">
                    <span id="zoom-level">100%</span>
                </button>
                <button type="button" class="btn-zoom" id="btn-zoom-in" data-bs-toggle="tooltip" title="Zoom In">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table-modern" id="userTable">
            <thead>
                <tr>
                    <th style="text-align:center;">#</th>
                    <th style="text-align:center;">Profil</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td class="text-muted fw-bold text-center"><?= $i + 1 ?></td>
                            <td class="text-center">
                                <div class="avatar-wrapper">
                                    <?php if (!empty($user['profile_photo'])): ?>
                                        <img src="<?= base_url($user['profile_photo']) ?>" alt="Profile" class="avatar-img">
                                    <?php else: ?>
                                        <div class="avatar-initial">
                                            <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold" style="color: var(--text-dark);"><?= esc($user['username']) ?></span>
                            </td>
                            <td class="text-muted"><?= esc($user['email']) ?></td>
                            <td>
                                <span class="badge-role fw-bold"><?= esc($user['role_name'] ?? '-') ?></span>
                            </td>
                            <td>
                                <?php if ((int) ($user['active'] ?? 0) === 1): ?>
                                    <span class="badge-soft badge-active">
                                        <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge-soft badge-inactive">
                                        <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted" style="font-size: 0.85rem;">
                                <?= esc(date('d M Y', strtotime($user['created_at'] ?? 'now'))) ?>
                                <br>
                                <small style="color: #94a3b8;"><?= esc(date('H:i', strtotime($user['created_at'] ?? 'now'))) ?></small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" class="btn-action-soft btn-edit-soft" data-bs-toggle="tooltip" title="Edit User">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form action="<?= site_url('admin/users/delete/' . $user['id']) ?>" method="post" class="form-delete no-loading m-0 p-0" data-no-loading="true">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-action-soft btn-delete-soft" data-bs-toggle="tooltip" title="Hapus User">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="padding: 60px 20px; text-align: center;">
                            <div style="color: #94a3b8;">
                                <i class="fa-solid fa-folder-open mb-3" style="font-size: 3.5rem; opacity: 0.4;"></i>
                                <h5 class="fw-bold text-dark">Data tidak ditemukan</h5>
                                <p class="mb-0 text-muted">Belum ada data user atau kata kunci tidak cocok.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tooltip initialization
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // ---------------------------------------------------------
        // FITUR ZOOM TABEL
        // ---------------------------------------------------------
        const table = document.getElementById('userTable');
        const zoomInBtn = document.getElementById('btn-zoom-in');
        const zoomOutBtn = document.getElementById('btn-zoom-out');
        const zoomResetBtn = document.getElementById('btn-zoom-reset');
        const zoomLevelText = document.getElementById('zoom-level');
        
        let currentZoom = 100;

        function applyZoom() {
            table.style.zoom = `${currentZoom}%`;
            zoomLevelText.textContent = `${currentZoom}%`;
        }

        zoomInBtn.addEventListener('click', () => {
            if (currentZoom < 150) { // Maksimal zoom 150%
                currentZoom += 10;
                applyZoom();
            }
        });

        zoomOutBtn.addEventListener('click', () => {
            if (currentZoom > 50) { // Minimal zoom 50%
                currentZoom -= 10;
                applyZoom();
            }
        });

        zoomResetBtn.addEventListener('click', () => {
            currentZoom = 100;
            applyZoom();
        });

        // ---------------------------------------------------------
        // SWEETALERT TOAST UNTUK FLASH DATA (SUCCESS / ERROR)
        // ---------------------------------------------------------
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if (session()->getFlashdata('success')): ?>
            Toast.fire({
                icon: 'success',
                title: '<?= esc(session()->getFlashdata('success')) ?>'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Toast.fire({
                icon: 'error',
                title: '<?= esc(session()->getFlashdata('error')) ?>'
            });
        <?php endif; ?>
        // ---------------------------------------------------------

        // SweetAlert untuk Konfirmasi Hapus Data
        const deleteForms = document.querySelectorAll('.form-delete');

        deleteForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (form.dataset.submitted === 'true') {
                    return;
                }

                e.preventDefault();

                Swal.fire({
                    title: 'Hapus akun ini?',
                    text: 'Data yang sudah dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-custom',
                        confirmButton: 'btn-swal-confirm px-4',
                        cancelButton: 'btn-swal-cancel px-4'
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    form.dataset.submitted = 'true';

                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            HTMLFormElement.prototype.submit.call(form);
                        }
                    });
                });
            });
        });
    });
</script>

<?= $this->endSection() ?>