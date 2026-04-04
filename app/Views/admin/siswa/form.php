<?= $this->extend('admin/template/layout') ?>

<?= $this->section('content') ?>

<?php
$isEdit = $mode === 'edit';
$formAction = $isEdit
    ? site_url('admin/siswa/update/' . $siswa['id'])
    : site_url('admin/siswa/store');

$oldOrValue = function ($field, $default = '') use ($siswa) {
    return old($field, $siswa[$field] ?? $default);
};

// Prediksi inisial untuk placeholder
$initials = 'S';
if ($isEdit && !empty($siswa['nama_siswa'])) {
    $words = explode(' ', $siswa['nama_siswa']);
    $initials = strtoupper(substr($words[0], 0, 1));
    if (count($words) > 1) {
        $initials .= strtoupper(substr($words[1], 0, 1));
    }
}

$errors = session('errors') ?? [];
?>

<style>
    :root {
        --primary: #4f46e5;
        /* Indigo */
        --primary-soft: #eef2ff;
        --secondary: #64748b;
        /* Slate */
        --success: #10b981;
        /* Emerald */
        --warning: #f59e0b;
        /* Amber */
        --danger: #ef4444;
        /* Red */
        --background: #f8fafc;
        --card-bg: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;

        /* Dynamic Accent based on Mode */
        --theme-color: <?= $isEdit ? 'var(--warning)' : 'var(--primary)' ?>;
        --theme-gradient: <?= $isEdit ? 'linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%)' : 'linear-gradient(135deg, #4f46e5 0%, #818cf8 100%)' ?>;
    }

    body {
        background-color: var(--background);
        color: var(--text-main);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    /* Main Card Styling */
    .main-form-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        background: var(--card-bg);
    }

    .card-header-gradient {
        background: var(--theme-gradient);
        color: white;
        padding: 2rem 1.5rem;
        border: none;
        position: relative;
    }

    .card-header-gradient h3 {
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .card-header-gradient::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 20px;
        background: var(--card-bg);
        border-radius: 20px 20px 0 0;
    }

    /* Section Styling */
    .form-section {
        margin-bottom: 2.5rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border);
    }

    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--theme-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    /* Input Styling */
    .form-group label {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--secondary);
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        background-color: white;
        color: var(--text-main);
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--theme-color);
        box-shadow: 0 0 0 4px <?= $isEdit ? 'rgba(245, 158, 11, 0.15)' : 'rgba(79, 70, 229, 0.1)' ?>;
    }

    /* Custom Radio Buttons */
    .gender-select {
        display: flex;
        gap: 10px;
    }

    .gender-option {
        flex: 1;
        position: relative;
    }

    .gender-option input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .gender-label {
        display: block;
        padding: 0.75rem;
        text-align: center;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
        color: var(--secondary);
    }

    .gender-option input:checked+.gender-label {
        background: var(--primary-soft);
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Modern File Input */
    .file-drop-area {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        padding: 1.5rem;
        background-color: var(--background);
        border: 2px dashed var(--border);
        border-radius: 12px;
        transition: 0.2s;
        cursor: pointer;
    }

    .file-drop-area:hover,
    .file-drop-area.is-active {
        border-color: var(--primary);
        background-color: var(--primary-soft);
    }

    .file-icon {
        font-size: 2rem;
        color: var(--secondary);
        margin-right: 1rem;
    }

    .file-msg {
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .file-input {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        opacity: 0;
        cursor: pointer;
    }

    /* Preview Sidebar */
    .preview-card {
        position: sticky;
        top: 20px;
        border: none;
        border-radius: 20px;
        background: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .preview-header {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        font-weight: 700;
        color: var(--text-main);
    }

    .preview-avatar-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 2rem auto;
    }

    .preview-avatar,
    .preview-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 30px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .preview-placeholder {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
    }

    .status-badge {
        position: absolute;
        bottom: -5px;
        right: -5px;
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .status-active {
        background-color: var(--success);
    }

    .status-inactive {
        background-color: var(--danger);
    }

    .preview-data-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .preview-data-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .preview-data-label {
        color: var(--text-muted);
    }

    .preview-data-value {
        font-weight: 600;
        color: var(--text-main);
        text-align: right;
        max-width: 60%;
    }

    /* Submit Button */
    .btn-submit {
        background: var(--theme-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 1rem 2.5rem;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s;
        box-shadow: 0 4px 10px <?= $isEdit ? 'rgba(245, 158, 11, 0.3)' : 'rgba(79, 70, 229, 0.3)' ?>;
    }

    .btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px <?= $isEdit ? 'rgba(245, 158, 11, 0.4)' : 'rgba(79, 70, 229, 0.4)' ?>;
        color: white;
    }

    .btn-submit:disabled {
        background: #cbd5e1;
        box-shadow: none;
        cursor: not-allowed;
    }

    /* Custom Toggle Switch Styling */
    .status-toggle-container {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-top: 8px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    input:checked+.slider {
        background-color: var(--success);
    }

    input:focus+.slider {
        box-shadow: 0 0 1px var(--success);
    }

    input:checked+.slider:before {
        transform: translateX(24px);
    }

    .status-label {
        font-weight: 700;
        font-size: 0.9rem;
        transition: color 0.3s;
    }

    /* Warna label dinamis */
    .text-active {
        color: var(--success);
    }

    .text-inactive {
        color: var(--danger);
    }
</style>

<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div class="d-flex gap-2">
            <?php if ($isEdit): ?>
                <span class="badge badge-warning p-2 rounded-pill">Mode Edit</span>
            <?php else: ?>
                <span class="badge badge-primary p-2 rounded-pill">Mode Tambah</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('error') || !empty($errors)): ?>
        <div class="alert alert-danger rounded-lg shadow-sm mb-4 p-3 d-flex align-items-start">
            <i class="fas fa-exclamation-circle mr-3 mt-1 font-size-lg"></i>
            <div>
                <strong class="d-block mb-1">Mohon perbaiki kesalahan berikut:</strong>
                <ul class="mb-0 pl-3 small">
                    <?php if (session()->getFlashdata('error')): ?>
                        <li><?= esc(session()->getFlashdata('error')) ?></li>
                    <?php endif; ?>
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card main-form-card">
                    <div class="card-header-gradient">
                        <h3 class="mb-1"><?= $isEdit ? 'Edit Data Siswa' : 'Pendaftaran Siswa Baru' ?></h3>
                        <p class="mb-0 opacity-75">Isi data akademik, identitas, dan keluarga pada form di bawah ini.</p>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-graduation-cap"></i></div>
                                <h4 class="section-title">Informasi Akademik</h4>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label>Kelas <span class="text-danger">*</span></label>
                                    <select name="kelas_id" id="kelas_id" class="form-select w-100" required>
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($kelasOptions as $kelas): ?>
                                            <option value="<?= $kelas['id'] ?>" <?= (string)$selectedKelasId === (string)$kelas['id'] ? 'selected' : '' ?>>
                                                <?= esc($kelas['nama_kelas']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label>Tahun Ajaran <span class="text-danger">*</span></label>
                                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select w-100" required>
                                        <option value="">Pilih Tahun Ajaran</option>
                                        <?php foreach ($tahunAjaranOptions as $tahun): ?>
                                            <option value="<?= $tahun['id'] ?>" <?= (string)$selectedTahunId === (string)$tahun['id'] ? 'selected' : '' ?>>
                                                <?= esc($tahun['nama_tahun_ajaran']) ?> <?= $tahun['is_active'] ? '(Aktif)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-8 form-group mb-3">
                                    <label>Wali Kelas <span class="text-danger">*</span></label>
                                    <select name="wali_kelas_user_id" id="wali_kelas_user_id" class="form-select w-100" required>
                                        <option value="">Pilih Wali Kelas</option>
                                        <?php foreach ($guruOptions as $guru): ?>
                                            <option value="<?= $guru['id'] ?>" <?= (string)$selectedWaliKelasId === (string)$guru['id'] ? 'selected' : '' ?>>
                                                <?= esc($guru['username'] ?: $guru['email']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label>Status Siswa</label>
                                    <div class="status-toggle-container">
                                        <label class="switch">
                                            <input type="checkbox" id="status_aktif" name="status_aktif" value="1" <?= (int)old('status_aktif', $siswa['status_aktif'] ?? 1) === 1 ? 'checked' : '' ?>>
                                            <span class="slider round"></span>
                                        </label>
                                        <span id="status-label-text" class="status-label">Aktif</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-id-card"></i></div>
                                <h4 class="section-title">Data Pribadi Siswa</h4>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label>NIS <span class="text-danger">*</span></label>
                                    <input type="text" name="nis" id="nis" class="form-control" value="<?= esc($oldOrValue('nis')) ?>" placeholder="Contoh: 2122001" required>
                                </div>
                                <div class="col-md-8 form-group mb-3">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_siswa" id="nama_siswa" class="form-control" value="<?= esc($oldOrValue('nama_siswa')) ?>" placeholder="Masukkan nama lengkap siswa sesuai ijazah" required>
                                </div>
                                <div class="col-md-12 form-group mb-3">
                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                    <div class="gender-select">
                                        <div class="gender-option">
                                            <input type="radio" name="jenis_kelamin" id="jk_l" value="L" <?= $oldOrValue('jenis_kelamin', 'L') === 'L' ? 'checked' : '' ?>>
                                            <label for="jk_l" class="gender-label">
                                                <i class="fas fa-mars mr-2"></i> Laki-laki
                                            </label>
                                        </div>
                                        <div class="gender-option">
                                            <input type="radio" name="jenis_kelamin" id="jk_p" value="P" <?= $oldOrValue('jenis_kelamin') === 'P' ? 'checked' : '' ?>>
                                            <label for="jk_p" class="gender-label">
                                                <i class="fas fa-venus mr-2"></i> Perempuan
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-users"></i></div>
                                <h4 class="section-title">Orang Tua / Wali & Kontak</h4>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label>Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_orang_tua" id="nama_orang_tua" class="form-control" value="<?= esc($oldOrValue('nama_orang_tua')) ?>" required>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label>Nomor HP (WhatsApp)</label>
                                    <input type="text" name="nomor_hp_orang_tua" id="nomor_hp_orang_tua" class="form-control" value="<?= esc($oldOrValue('nomor_hp_orang_tua')) ?>" placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-md-12 form-group mb-3">
                                    <label>Alamat Lengkap</label>
                                    <textarea name="alamat" id="alamat" rows="3" class="form-control" placeholder="Nama jalan, RT/RW, Kec, Kota/Kab..."><?= esc($oldOrValue('alamat')) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-section mb-0">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-camera"></i></div>
                                <h4 class="section-title">Foto Siswa</h4>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <div class="file-drop-area" id="fileDropArea">
                                        <i class="fas fa-cloud-upload-alt file-icon"></i>
                                        <div class="file-info">
                                            <span class="file-msg font-weight-bold d-block text-dark">Tarik foto ke sini atau klik untuk memilih</span>
                                            <span class="small text-muted">Format: JPG, JPEG, PNG, WEBP. Maks 2MB.</span>
                                        </div>
                                        <input type="file" name="gambar_siswa" class="file-input" id="gambar_siswa" accept="image/jpeg,image/png,image/webp">
                                    </div>
                                    <div id="fileNameDisplay" class="small text-primary mt-2 font-weight-bold"></div>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-5 pt-4 border-top">
                            <button type="submit" id="btnSubmit" class="btn-submit btn-lg">
                                <i class="fa fa-save mr-2"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Data' ?>
                            </button>


                            <a href="<?= site_url('admin/siswa') ?>" class="btn btn-secondary btn-lg">
                                <i class="fa fa-arrow-left mr-1"></i> Batal
                            </a>

                        </div>


                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card preview-card shadow-sm">
                    <div class="preview-header">Ringkasan Draft</div>
                    <div class="card-body p-4">
                        <div class="preview-avatar-container">
                            <?php if ($isEdit && !empty($siswa['gambar_siswa'])): ?>
                                <img id="previewImage" src="<?= base_url($siswa['gambar_siswa']) ?>" class="preview-avatar">
                            <?php else: ?>
                                <div id="previewPlaceholder" class="preview-placeholder"><?= $initials ?></div>
                                <img id="previewImage" src="" class="preview-avatar d-none">
                            <?php endif; ?>
                            <span id="previewStatusBadge" class="status-badge status-active">Aktif</span>
                        </div>

                        <div class="text-center mb-4">
                            <h5 id="previewNama" class="font-weight-bold mb-1 text-truncate"><?= $oldOrValue('nama_siswa', 'Nama Siswa') ?></h5>
                            <span id="previewNis" class="text-muted small">NIS: <?= $oldOrValue('nis', '-') ?></span>
                        </div>

                        <div class="bg-light rounded-lg p-3 mb-3 border">
                            <div class="small text-muted mb-1">Kelas & Tahun Ajaran</div>
                            <div id="previewKta" class="font-weight-bold text-dark">-</div>
                        </div>

                        <ul class="preview-data-list">
                            <li class="preview-data-item">
                                <span class="preview-data-label">Jenis Kelamin</span>
                                <span id="previewJk" class="preview-data-value">-</span>
                            </li>
                            <li class="preview-data-item">
                                <span class="preview-data-label">Wali Kelas</span>
                                <span id="previewWali" class="preview-data-value text-truncate">-</span>
                            </li>
                            <li class="preview-data-item">
                                <span class="preview-data-label">Orang Tua</span>
                                <span id="previewOrtu" class="preview-data-value text-truncate">-</span>
                            </li>
                            <li class="preview-data-item">
                                <span class="preview-data-label">No. HP</span>
                                <span id="previewHp" class="preview-data-value">-</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const formInputs = {
            kelas: document.getElementById('kelas_id'),
            tahun: document.getElementById('tahun_ajaran_id'),
            wali: document.getElementById('wali_kelas_user_id'),
            nis: document.getElementById('nis'),
            nama: document.getElementById('nama_siswa'),
            ortu: document.getElementById('nama_orang_tua'),
            hp: document.getElementById('nomor_hp_orang_tua'),
            status: document.getElementById('status_aktif'),
            image: document.getElementById('gambar_siswa')
        };

        const previews = {
            kta: document.getElementById('previewKta'),
            wali: document.getElementById('previewWali'),
            nis: document.getElementById('previewNis'),
            nama: document.getElementById('previewNama'),
            jk: document.getElementById('previewJk'),
            ortu: document.getElementById('previewOrtu'),
            hp: document.getElementById('previewHp'),
            image: document.getElementById('previewImage'),
            placeholder: document.getElementById('previewPlaceholder'),
            statusBadge: document.getElementById('previewStatusBadge')
        };

        const btnSubmit = document.getElementById('btnSubmit');
        const fileDropArea = document.getElementById('fileDropArea');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        // Functions
        function getGenderText() {
            const checked = document.querySelector('input[name="jenis_kelamin"]:checked');
            if (!checked) return '-';
            return checked.value === 'L' ? 'Laki-laki' : 'Perempuan';
        }

        function getInitials(name) {
            if (!name) return 'S';
            const words = name.trim().split(' ');
            let initials = words[0].charAt(0).toUpperCase();
            if (words.length > 1) {
                initials += words[1].charAt(0).toUpperCase();
            }
            return initials;
        }

        function refreshPreview() {
            // Akademik
            const k = formInputs.kelas.selectedIndex > 0 ? formInputs.kelas.options[formInputs.kelas.selectedIndex].text : '';

            // Ambil teks dari select Tahun, lalu HAPUS kata "(Aktif)" jika ada
            let t = formInputs.tahun.selectedIndex > 0 ? formInputs.tahun.options[formInputs.tahun.selectedIndex].text : '';
            t = t.replace('(Aktif)', '').trim(); // Ini akan menghilangkan teks "(Aktif)"
            const w = formInputs.wali.selectedIndex > 0 ? formInputs.wali.options[formInputs.wali.selectedIndex].text : '-';

            const hasSelection = formInputs.kelas.value !== '' && formInputs.tahun.value !== '';
            previews.kta.textContent = hasSelection ? `${k} (${t})` : 'Belum ditentukan';
            if (hasSelection) {
                previews.kta.textContent = `${k} (${t})`;
                previews.kta.classList.remove('text-muted');
            } else {
                previews.kta.textContent = 'Belum ditentukan';
                previews.kta.classList.add('text-muted');
            }

            previews.wali.textContent = w;

            // Pribadi
            previews.nis.textContent = `NIS: ${formInputs.nis.value.trim() || '-'}`;
            const nama = formInputs.nama.value.trim();
            previews.nama.textContent = nama || 'Nama Siswa';
            previews.jk.textContent = getGenderText();

            // Placeholder Initials
            if (previews.placeholder) {
                previews.placeholder.textContent = getInitials(nama);
            }

            // Keluarga
            previews.ortu.textContent = formInputs.ortu.value.trim() || '-';
            previews.hp.textContent = formInputs.hp.value.trim() || '-';

            // Status
            if (formInputs.status.checked) {
                previews.statusBadge.textContent = 'Aktif';
                previews.statusBadge.className = 'status-badge status-active';

                if (statusLabelText) {
                    statusLabelText.textContent = 'Aktif';
                    statusLabelText.className = 'status-label text-active';
                }
            } else {
                previews.statusBadge.textContent = 'Nonaktif';
                previews.statusBadge.className = 'status-badge status-inactive';

                if (statusLabelText) {
                    statusLabelText.textContent = 'Nonaktif';
                    statusLabelText.className = 'status-label text-inactive';
                }
            }

            // Submit button state
            btnSubmit.disabled = !hasSelection;
        }

        // Image Preview Logic
        function readURL(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previews.placeholder) previews.placeholder.classList.add('d-none');
                    previews.image.src = e.target.result;
                    previews.image.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
                fileNameDisplay.textContent = `File terpilih: ${input.files[0].name}`;
            }
        }

        // Event Listeners
        const inputEvents = ['input', 'change'];
        Object.values(formInputs).forEach(input => {
            if (!input) return;
            inputEvents.forEach(evt => input.addEventListener(evt, refreshPreview));
        });

        document.querySelectorAll('input[name="jenis_kelamin"]').forEach(el => {
            el.addEventListener('change', refreshPreview);
        });

        // Modern File Input Interactivity
        formInputs.image.addEventListener('change', function() {
            readURL(this);
        });

        // Drag & Drop visual state
        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, () => fileDropArea.classList.add('is-active'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, () => fileDropArea.classList.remove('is-active'), false);
        });

        // Init
        refreshPreview();
    });
</script>

<?= $this->endSection() ?>