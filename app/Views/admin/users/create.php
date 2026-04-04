<?= $this->extend('admin/template/layout') ?>

<?= $this->section('content') ?>

<style>
    .image-preview-wrapper {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 2px dashed #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f9fa;
        margin-bottom: 15px;
    }

    .image-preview-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    .image-preview-placeholder {
        color: #adb5bd;
        font-size: 14px;
        text-align: center;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }
</style>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4 p-md-5">

        <div class="mb-4">
            <h3 class="mb-1 fw-bold text-primary">Tambah User</h3>
            <p class="text-muted">Buat akun baru untuk admin, kepala sekolah, atau guru.</p>
            <hr>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <form action="<?= site_url('admin/users/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-4 col-xl-3 d-flex flex-column align-items-center mb-4">
                    <label class="form-label text-center w-100 mb-3">Foto Profil</label>
                    <div class="image-preview-wrapper" id="preview-wrapper">
                        <span class="image-preview-placeholder" id="preview-placeholder">Preview<br>Foto</span>
                        <img id="image-preview" src="" alt="Preview">
                    </div>
                    <input type="file" name="profile_photo" id="profile_photo" accept=".jpg,.jpeg,.png,.webp" class="form-control form-control-sm <?= isset($errors['profile_photo']) ? 'is-invalid' : '' ?>" onchange="previewImage(event)">
                    <small class="text-muted mt-2 text-center">Format: JPG, PNG, WEBP.<br>Maks. 2 MB.</small>
                    <?php if (isset($errors['profile_photo'])): ?>
                        <div class="invalid-feedback d-block text-center"><?= esc($errors['profile_photo']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-8 col-xl-9">

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" value="<?= old('username') ?>" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" placeholder="Masukkan username">
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="<?= old('email') ?>" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="contoh@sekolah.com">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>">
                            <option value="">-- Pilih Peran / Role --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= esc($role['name']) ?>" <?= old('role') === $role['name'] ? 'selected' : '' ?>>
                                    <?= esc(ucfirst($role['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['role'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Masukkan password">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirm" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" placeholder="Ketik ulang password">
                            <?php if (isset($errors['password_confirm'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['password_confirm']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2 border-top pt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('admin/users') ?>" class="btn btn-light border px-4">
                            Batal
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        const reader = new FileReader();
        const previewImg = document.getElementById('image-preview');
        const placeholder = document.getElementById('preview-placeholder');
        const wrapper = document.getElementById('preview-wrapper');

        if (input.files && input.files[0]) {
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                placeholder.style.display = 'none';
                wrapper.style.border = 'none'; // Menghilangkan border dashed saat ada gambar
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            // Reset jika user batal memilih file
            previewImg.src = '';
            previewImg.style.display = 'none';
            placeholder.style.display = 'block';
            wrapper.style.border = '2px dashed #ccc';
        }
    }
</script>

<?= $this->endSection() ?>