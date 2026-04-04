<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style>
	body {
		background: #f4f7f6;
		font-family: 'Poppins', sans-serif;
	}

	.login-wrapper {
		min-height: calc(80vh - 80px);
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 4vh 15px;
		position: relative;
	}

	.login-card {
		border: none;
		border-radius: 24px;
		box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
		overflow: hidden;
		background: #fff;
		max-width: 950px;
		width: 100%;
		transform: translateY(0);
		transition: all 0.4s ease;
		margin: auto;
		position: relative;
		z-index: 2;
	}

	.login-card:hover {
		box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
	}

	.login-visual {
		background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
		color: white;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: 50px;
		position: relative;
		overflow: hidden;
	}

	.visual-icon {
		font-size: 85px;
		margin-bottom: 25px;
		filter: drop-shadow(0 15px 20px rgba(0, 0, 0, 0.3));
		animation: floatIcon 3s ease-in-out infinite;
	}

	.circle-decoration {
		position: absolute;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.1);
		backdrop-filter: blur(5px);
	}

	.circle-1 {
		bottom: -50px;
		left: -50px;
		width: 250px;
		height: 250px;
	}

	.circle-2 {
		top: -30px;
		right: -30px;
		width: 150px;
		height: 150px;
	}

	.input-group {
		border-radius: 12px;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
	}

	.input-group-text {
		background-color: #f8f9fc;
		border: 2px solid #edf2f9;
		border-right: none;
		color: #a0aec0;
		border-radius: 12px 0 0 12px;
		transition: all 0.3s ease;
	}

	.form-control {
		border: 2px solid #edf2f9;
		border-left: none;
		border-radius: 0 12px 12px 0;
		padding: 14px 16px;
		font-size: 0.95rem;
		background-color: #f8f9fc;
		transition: all 0.3s ease;
		padding: 30px;
		font-size: 18px;
	}

	.form-control:focus {
		box-shadow: none;
		background-color: #fff;
	}

	.input-group:focus-within {
		transform: translateY(-3px);
		box-shadow: 0 10px 20px rgba(37, 99, 235, 0.1);
	}

	.input-group:focus-within .input-group-text,
	.input-group:focus-within .form-control {
		border-color: #2563eb;
		background-color: #fff;
		color: #2563eb;
	}

	.btn-login {
		border-radius: 12px;
		padding: 14px;
		font-weight: 600;
		font-size: 1rem;
		letter-spacing: 0.5px;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		background: #2563eb;
		border: none;
		position: relative;
		overflow: hidden;
	}

	.btn-login:hover {
		background: #1d4ed8;
		transform: translateY(-3px);
		box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
	}

	.btn-login:active {
		transform: translateY(1px);
	}

	.btn-login:disabled {
		opacity: 0.85;
		cursor: not-allowed;
		transform: none !important;
		box-shadow: none !important;
	}

	.password-toggle {
		cursor: pointer;
		transition: 0.2s;
		border-left: none !important;
		border-radius: 0 12px 12px 0;
	}

	.password-toggle:hover {
		color: #2563eb;
	}

	.form-control.border-right-0 {
		border-right: none;
		border-radius: 0;
	}

	.stagger-item {
		opacity: 0;
		animation: fadeInUpCustom 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
	}

	.stagger-1 {
		animation-delay: 0.1s;
	}

	.stagger-2 {
		animation-delay: 0.2s;
	}

	.stagger-3 {
		animation-delay: 0.3s;
	}

	.stagger-4 {
		animation-delay: 0.4s;
	}

	.stagger-5 {
		animation-delay: 0.5s;
	}

	.login-loading-overlay {
		position: absolute;
		inset: 0;
		background: rgba(255, 255, 255, 0.65);
		backdrop-filter: blur(4px);
		display: none;
		align-items: center;
		justify-content: center;
		z-index: 10;
		border-radius: 24px;
	}

	.login-loading-overlay.active {
		display: flex;
	}

	.loading-box {
		background: #fff;
		padding: 18px 22px;
		border-radius: 16px;
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
		display: flex;
		align-items: center;
		gap: 12px;
		font-weight: 600;
		color: #2563eb;
	}

	@keyframes fadeInUpCustom {
		0% {
			opacity: 0;
			transform: translateY(20px);
		}

		100% {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@keyframes floatIcon {

		0%,
		100% {
			transform: translateY(0);
		}

		50% {
			transform: translateY(-15px);
		}
	}

	@media (max-width: 768px) {
		.col-md-7.p-4.p-lg-5 {
			padding: 2rem 1.5rem !important;
		}

		.mb-5.d-md-none {
			margin-bottom: 2rem !important;
		}

		.mb-4 {
			margin-bottom: 1.25rem !important;
		}

		.login-wrapper {
			padding: 2vh 15px;
		}
	}
</style>

<div class="container">
	<div class="login-wrapper">

		<div class="login-loading-overlay" id="loginLoadingOverlay">
			<div class="loading-box">
				<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
				<span>Sedang masuk...</span>
			</div>
		</div>

		<div class="card login-card animate__animated animate__zoomIn animate__faster">
			<div class="row g-0">

				<div class="col-md-5 d-none d-md-flex login-visual text-center">
					<div class="circle-decoration circle-1"></div>
					<div class="circle-decoration circle-2"></div>
					<div style="z-index: 1;">
						<i class="fas fa-graduation-cap visual-icon"></i>
						<h2 class="fw-bold mb-3 tracking-wide">TK KARTINI</h2>
						<p class="lead text-white-50 fs-6">Sistem Pembayaran SPP</p>
					</div>
				</div>

				<div class="col-md-7 p-4 p-lg-5">
					<div class="text-center mb-5 d-md-none">
						<i class="fas fa-graduation-cap text-primary fs-1 mb-2"></i>
						<h3 class="fw-bold text-primary">TK KARTINI BATANG</h3>
					</div>

					<div class="stagger-item stagger-1">
						<h3 class="fw-bold mb-1">Selamat Datang</h3>
					</div>

					<div class="stagger-item stagger-2">
						<?= view('App\Views\Auth\_message_block') ?>
					</div>

					<form action="<?= url_to('login') ?>" method="post" id="loginForm">
						<?= csrf_field() ?>

						<div class="mb-4 stagger-item stagger-3">
							<label class="form-label small fw-semibold text-uppercase text-muted mb-2">Identitas Pengguna</label>
							<div class="input-group">
								<span class="input-group-text"><i class="fas fa-user"></i></span>
								<input
									type="text"
									name="login"
									class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
									placeholder="Username atau Email"
									autofocus>
							</div>
							<div class="invalid-feedback d-block"><?= session('errors.login') ?></div>
						</div>

						<div class="mb-4 stagger-item stagger-4">
							<div class="d-flex justify-content-between mb-2">
								<label class="form-label small fw-semibold text-uppercase text-muted mb-0">Kata Sandi</label>
							</div>
							<div class="input-group">
								<span class="input-group-text"><i class="fas fa-lock"></i></span>
								<input
									type="password"
									name="password"
									id="passwordField"
									class="form-control border-right-0 <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>"
									placeholder="Masukkan kata sandi">
								<span class="input-group-text bg-white password-toggle <?php if (session('errors.password')) : ?>border-danger<?php endif ?>" onclick="togglePassword()">
									<i class="fas fa-eye text-muted" id="eyeIcon"></i>
								</span>
							</div>
							<div class="invalid-feedback d-block"><?= session('errors.password') ?></div>
						</div>

						<div class="stagger-item stagger-5">
							<div class="d-flex justify-content-between align-items-center mb-4">
								<?php if ($config->allowRemembering): ?>
									<div class="form-check">
										<input type="checkbox" name="remember" class="form-check-input" id="rememberMe" <?php if (old('remember')) : ?> checked <?php endif ?>>
										<label class="form-check-label small text-muted mt-1" for="rememberMe">Ingat Saya</label>
									</div>
								<?php endif; ?>
							</div>

							<button type="submit" class="btn btn-primary w-100 btn-login mb-4" id="loginBtn">
								<span id="loginBtnText">
									Masuk Ke Sistem <i class="fas fa-arrow-right ms-2"></i>
								</span>
								<span id="loginBtnLoading" class="d-none">
									<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
									Memproses...
								</span>
							</button>

							<?php if ($config->allowRegistration) : ?>
								<p class="text-center text-muted small mb-0">
									TK Kartini Batang
								</p>
							<?php endif; ?>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
	function togglePassword() {
		const passwordField = document.getElementById('passwordField');
		const eyeIcon = document.getElementById('eyeIcon');

		if (passwordField.type === 'password') {
			passwordField.type = 'text';
			eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
			eyeIcon.classList.replace('text-muted', 'text-primary');
		} else {
			passwordField.type = 'password';
			eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
			eyeIcon.classList.replace('text-primary', 'text-muted');
		}
	}

	document.addEventListener('DOMContentLoaded', function() {
		const loginForm = document.getElementById('loginForm');
		const loginBtn = document.getElementById('loginBtn');
		const loginBtnText = document.getElementById('loginBtnText');
		const loginBtnLoading = document.getElementById('loginBtnLoading');
		const loadingOverlay = document.getElementById('loginLoadingOverlay');

		if (loginForm) {
			loginForm.addEventListener('submit', function() {
				loginBtn.disabled = true;
				loginBtnText.classList.add('d-none');
				loginBtnLoading.classList.remove('d-none');
				loadingOverlay.classList.add('active');
			});
		}
	});
</script>

<?= $this->endSection() ?>