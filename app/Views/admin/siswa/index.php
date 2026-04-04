<?= $this->extend('admin/template/layout') ?>

<?= $this->section('content') ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Modern Variable Colors */
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --surface: #ffffff;
        --background: #f8fafc;
        --border: #e2e8f0;
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    /* Student Thumbnails */
    .student-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid var(--surface);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .student-thumb-placeholder {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eef2ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        border: 1px solid #c7d2fe;
    }

    /* Cards */
    .toolbar-card,
    .table-card {
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        background: var(--surface);
        transition: box-shadow 0.3s ease;
    }

    .toolbar-card:hover,
    .table-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
    }

    /* Modern Table */
    .table-container {
        transition: all 0.3s ease;
        font-size: 14px;
        /* Default size */
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        color: var(--text-main);
    }

    .table-modern thead th {
        background: var(--background);
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85em;
        letter-spacing: 0.5px;
        padding: 16px;
        border-bottom: 2px solid var(--border);
        border-top: none;
        white-space: nowrap;
    }

    .table-modern tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }

    .table-modern tbody tr:hover td {
        background-color: #f8fafc;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* Zoom Controls (Pembaruan UI) */
    .zoom-controls {
        background: var(--background);
        border: 1px solid var(--border);
        padding: 4px;
        border-radius: 50px;
    }

    .zoom-controls .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--text-muted);
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .zoom-controls .btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Modern Pagination (Diperbagus) */
    .pagination {
        gap: 8px;
        margin-bottom: 0;
        flex-wrap: wrap;
    }

    .pagination .page-item .page-link {
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        color: var(--text-muted);
        background: var(--background);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .pagination .page-item.active .page-link {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 8px rgba(79, 70, 229, 0.4);
        transform: translateY(-2px);
    }

    .pagination .page-item .page-link:hover:not(.active) {
        background: var(--surface);
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    /* Checkbox styling */
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        cursor: pointer;
    }
</style>

<div class="">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="mb-1 font-weight-bold text-dark">Data Siswa</h3>
            <p class="text-muted mb-0"></i>Kelola data siswa dengan filter, edit massal, dan kendali zoom tabel.</p>
        </div>
        <a href="<?= site_url('admin/siswa/create') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa fa-plus mr-2"></i> Tambah Siswa
        </a>
    </div>

    <div class="card toolbar-card mb-4">
        <div class="card-body p-4">
            <form method="get" action="<?= site_url('admin/siswa') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold text-muted small">Cari Data</label>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control border-left-0 pl-0" placeholder="NIS, Nama Siswa, dll..." value="<?= esc($filters['q'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label font-weight-bold text-muted small">Kelas & Tahun</label>
                        <select name="kelas_tahun_ajaran_id" class="form-control custom-select">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($kelasTahunAjaranOptions as $opt): ?>
                                <option value="<?= esc($opt['id']) ?>" <?= ($filters['kelas_tahun_ajaran_id'] ?? '') == $opt['id'] ? 'selected' : '' ?>>
                                    <?= esc($opt['nama_kelas']) ?> - <?= esc($opt['nama_tahun_ajaran']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label font-weight-bold text-muted small">Wali Kelas</label>
                        <select name="wali_kelas_user_id" class="form-control custom-select">
                            <option value="">Semua Wali Kelas</option>
                            <?php foreach ($guruOptions as $guru): ?>
                                <option value="<?= esc($guru['id']) ?>" <?= ($filters['wali_kelas_user_id'] ?? '') == $guru['id'] ? 'selected' : '' ?>>
                                    <?= esc($guru['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label font-weight-bold text-muted small">Status</label>
                        <select name="status" class="form-control custom-select">
                            <option value="">Semua</option>
                            <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label font-weight-bold text-muted small">Baris</label>
                        <select name="per_page" class="form-control custom-select">
                            <?php foreach ([10, 25, 50, 100] as $pp): ?>
                                <option value="<?= $pp ?>" <?= (int)($filters['per_page'] ?? 10) === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="">

                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-dark ml-2 shadow-sm " title="Terapkan Filter">
                        <i class="fa fa-search mr-1"></i> Filter
                    </button>
                    <a href="<?= site_url('admin/siswa') ?>" class="btn btn-danger btn-sm  px-3 shadow-sm transition-all" style="margin-left: 8px;" title="Reset Filter">
                        <i class="fa fa-sync-alt mr-1"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>
    </div>

    <form id="bulkActionForm" method="post" action="<?= site_url('admin/siswa/bulk-delete') ?>">
        <?= csrf_field() ?>

        <div class="card table-card">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="bulk-actions">
                    <span class="text-muted mr-3 small"><span id="selectedCount" class="font-weight-bold text-dark">0</span> terpilih</span>

                    <button type="button" id="btnEditSelected" class="btn btn-sm btn-outline-warning mr-1 rounded-pill px-3" disabled>
                        <i class="fa fa-pen mr-1"></i> Edit
                    </button>
                    <button type="button" id="btnActivateSelected" class="btn btn-sm btn-outline-success mr-1 rounded-pill px-3" disabled>
                        <i class="fa fa-check mr-1"></i> Aktifkan
                    </button>
                    <button type="button" id="btnDeactivateSelected" class="btn btn-sm btn-outline-secondary mr-1 rounded-pill px-3" disabled>
                        <i class="fa fa-ban mr-1"></i> Nonaktifkan
                    </button>
                    <button type="button" id="btnDeleteSelected" class="btn btn-sm btn-outline-danger rounded-pill px-3" disabled>
                        <i class="fa fa-trash mr-1"></i> Hapus
                    </button>
                </div>

                <div class="zoom-controls d-flex align-items-center shadow-sm">
                    <span class="text-muted small mr-2 d-none d-md-inline ml-2"><i class="fa fa-search-plus"></i> Tabel:</span>
                    <button type="button" class="btn mr-1" id="btnZoomOut" title="Perkecil Tabel"><i class="fa fa-minus"></i></button>
                    <span id="zoomPercentage" class="mx-2 font-weight-bold text-primary" style="min-width: 45px; text-align: center;">100%</span>
                    <button type="button" class="btn" id="btnZoomIn" title="Perbesar Tabel"><i class="fa fa-plus"></i></button>
                </div>
            </div>

            <div class="card-body px-0 pt-3">
                <div class="table-responsive px-4">
                    <div class="table-container" id="zoomableTable">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" class="form-check-input">
                                    </th>
                                    <th width="50">#</th>
                                    <th>Profil</th>
                                    <th>Data Siswa</th>
                                    <th>JK</th>
                                    <th>Kelas & Tahun</th>
                                    <th>Orang Tua</th>
                                    <th>Status</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (! empty($siswaList)): ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($siswaList as $row): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input row-check" name="selected_ids[]" value="<?= esc($row['id']) ?>">
                                            </td>
                                            <td class="text-muted"><?= $no++ ?></td>
                                            <td>
                                                <?php if (! empty($row['gambar_siswa'])): ?>
                                                    <img src="<?= base_url($row['gambar_siswa']) ?>" alt="Foto" class="student-thumb">
                                                <?php else: ?>
                                                    <div class="student-thumb-placeholder shadow-sm">
                                                        <?= esc(strtoupper(substr($row['nama_siswa'], 0, 1))) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-dark"><?= esc($row['nama_siswa']) ?></div>
                                                <div class="text-muted small"><i class="fa fa-id-card mr-1"></i> <?= esc($row['nis']) ?></div>
                                            </td>
                                            <td><?= esc($row['jenis_kelamin'] === 'L' ? 'L' : 'P') ?></td>
                                            <td>
                                                <div class="font-weight-medium text-dark"><?= esc($row['nama_kelas'] ?? '-') ?></div>
                                                <div class="text-muted small mt-1"><i class="fa fa-user mr-1"></i> Wali Kelas: <?= esc($row['wali_kelas'] ?? '-') ?></div>
                                                <span class="badge badge-light border text-muted mt-1"><?= esc($row['nama_tahun_ajaran'] ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <div class="text-dark"><?= esc($row['nama_orang_tua']) ?></div>
                                                <div class="text-muted small"><i class="fa fa-phone mr-1"></i> <?= esc($row['nomor_hp_orang_tua'] ?: '-') ?></div>
                                            </td>
                                            <td>
                                                <?php if ((int) $row['status_aktif'] === 1): ?>
                                                    <span class=" badge-success px-2 py-1 rounded-pill"> Aktif</span>
                                                <?php else: ?>
                                                    <span class=" badge-secondary px-2 py-1 rounded-pill">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group shadow-sm rounded-pill">
                                                    <a href="<?= site_url('admin/siswa/edit/' . $row['id']) ?>" class="btn btn-sm btn-light border hover-primary" data-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-pen text-primary"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-light border btn-delete-single" data-id="<?= $row['id'] ?>" data-toggle="tooltip" title="Hapus">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <i class="fa fa-folder-open fa-3x mb-3 text-light"></i><br>
                                            Belum ada data siswa ditemukan.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center px-4 mt-4 pb-3 flex-wrap">
                    <div class="text-muted small mb-3 mb-md-0">
                        Menampilkan data siswa berdasarkan filter.
                    </div>
                    <div>
                        <?= $pager->links() ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<form id="formDeleteSingle" method="post" style="display: none;">
    <?= csrf_field() ?>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. SETUP SWEETALERT TOAST ---
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Tampilkan Notifikasi dari Flashdata Session CI4
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


        // --- 2. FITUR ZOOM IN / ZOOM OUT TABEL (Dengan Persentase) ---
        const tableContainer = document.getElementById('zoomableTable');
        const zoomPercentageDisplay = document.getElementById('zoomPercentage');
        const baseFontSize = 14;
        let currentFontSize = baseFontSize;

        function updateZoom() {
            tableContainer.style.fontSize = currentFontSize + 'px';
            // Hitung persentase berdasarkan font awal (14px = 100%)
            const percentage = Math.round((currentFontSize / baseFontSize) * 100);
            zoomPercentageDisplay.textContent = percentage + '%';
        }

        document.getElementById('btnZoomIn').addEventListener('click', function() {
            if (currentFontSize < 22) {
                currentFontSize += 1.4; // Naik sekitar 10% setiap klik
                updateZoom();
            }
        });

        document.getElementById('btnZoomOut').addEventListener('click', function() {
            if (currentFontSize > 10) {
                currentFontSize -= 1.4; // Turun sekitar 10% setiap klik
                updateZoom();
            }
        });


        // --- 3. LOGIKA CHECKBOX & BULK ACTIONS ---
        const checkAll = document.getElementById('checkAll');
        const rowChecks = Array.from(document.querySelectorAll('.row-check'));
        const selectedCount = document.getElementById('selectedCount');

        const btnEditSelected = document.getElementById('btnEditSelected');
        const btnActivateSelected = document.getElementById('btnActivateSelected');
        const btnDeactivateSelected = document.getElementById('btnDeactivateSelected');
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        const bulkActionForm = document.getElementById('bulkActionForm');

        const bulkRoutes = {
            activate: "<?= site_url('admin/siswa/bulk-activate') ?>",
            deactivate: "<?= site_url('admin/siswa/bulk-deactivate') ?>",
            delete: "<?= site_url('admin/siswa/bulk-delete') ?>"
        };

        function getSelectedIds() {
            return rowChecks.filter(el => el.checked).map(el => el.value);
        }

        function refreshBulkState() {
            const selectedIds = getSelectedIds();
            const totalSelected = selectedIds.length;

            if (selectedCount) selectedCount.textContent = totalSelected;
            if (btnEditSelected) btnEditSelected.disabled = totalSelected !== 1;
            if (btnActivateSelected) btnActivateSelected.disabled = totalSelected < 1;
            if (btnDeactivateSelected) btnDeactivateSelected.disabled = totalSelected < 1;
            if (btnDeleteSelected) btnDeleteSelected.disabled = totalSelected < 1;

            if (checkAll) {
                const checkedCount = rowChecks.filter(el => el.checked).length;
                checkAll.checked = rowChecks.length > 0 && checkedCount === rowChecks.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                rowChecks.forEach(el => el.checked = checkAll.checked);
                refreshBulkState();
            });
        }

        rowChecks.forEach(el => {
            el.addEventListener('change', refreshBulkState);
        });

        // Eksekusi Bulk Action menggunakan SweetAlert
        function submitBulk(action, confirmTitle, confirmText, confirmBtnClass) {
            const selectedIds = getSelectedIds();
            if (selectedIds.length < 1) return;

            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmBtnClass === 'danger' ? '#ef4444' : '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkActionForm.action = bulkRoutes[action];
                    bulkActionForm.submit();
                }
            });
        }

        if (btnEditSelected) {
            btnEditSelected.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length === 1) {
                    window.location.href = "<?= site_url('admin/siswa/edit') ?>/" + selectedIds[0];
                }
            });
        }

        if (btnActivateSelected) {
            btnActivateSelected.addEventListener('click', () => {
                submitBulk('activate', 'Aktifkan Siswa?', `Anda akan mengaktifkan ${getSelectedIds().length} data siswa.`, 'success');
            });
        }

        if (btnDeactivateSelected) {
            btnDeactivateSelected.addEventListener('click', () => {
                submitBulk('deactivate', 'Nonaktifkan Siswa?', `Anda akan menonaktifkan ${getSelectedIds().length} data siswa.`, 'warning');
            });
        }

        if (btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', () => {
                submitBulk('delete', 'Hapus Data?', `Anda akan menghapus permanen ${getSelectedIds().length} data siswa. Ini tidak bisa dibatalkan!`, 'danger');
            });
        }

        // --- 4. SINGLE DELETE DENGAN SWEETALERT ---
        document.querySelectorAll('.btn-delete-single').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Hapus Siswa Ini?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('formDeleteSingle');
                        form.action = "<?= site_url('admin/siswa/delete/') ?>" + id;
                        form.submit();
                    }
                });
            });
        });

        refreshBulkState();
    });
</script>
<?= $this->endSection() ?>