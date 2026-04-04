<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
        crossorigin="anonymous">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary-color: #3757e9;
            --hover-bg-color: #ffffff;
            --hover-text-color: #3757e9;
            --text-color-white: #ffffff;
            --text-color-muted: rgba(255, 255, 255, 0.7);
            --bg-body: #f0f2f5;
            --sidebar-width: 280px;
        }

        body {
            background-color: var(--bg-body);
            display: flex;
            overflow-x: hidden;
        }

        .page-loading {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1055;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .page-loading.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .page-loading-box {
            background: #ffffff;
            padding: 18px 24px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
            color: var(--primary-color);
            font-weight: 600;
            font-size: 15px;
        }

        .page-loading-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(55, 87, 233, 0.18);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
            flex-shrink: 0;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .swal2-container {
            z-index: 20000 !important;
        }

        body.swal2-shown .page-loading.active,
        body.swal2-height-auto .page-loading.active {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            display: flex;
            flex-direction: column;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 100;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }

        .logo-details {
            display: flex;
            align-items: center;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-color-white);
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .logo-details i {
            font-size: 28px;
            margin-right: 12px;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-profile-short {
            display: flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.1);
            padding: 10px;
            border-radius: 12px;
        }

        .user-profile-short img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .user-name-role {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-color-white);
        }

        .user-role {
            font-size: 12px;
            color: var(--text-color-muted);
        }

        .nav-links {
            list-style: none;
            padding: 0 15px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-links::-webkit-scrollbar {
            width: 0;
        }

        .nav-links li {
            position: relative;
            margin-bottom: 8px;
        }

        .nav-links li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-color-white);
            padding: 14px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 400;
            font-size: 15px;
        }

        .nav-links li a i {
            font-size: 18px;
            margin-right: 15px;
            min-width: 25px;
            text-align: center;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .nav-links li a:hover {
            background-color: var(--hover-bg-color);
            color: var(--hover-text-color);
            font-weight: 500;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-links li a:hover i {
            color: var(--hover-text-color);
            opacity: 1;
            transform: scale(1.1);
        }

        .nav-links li a.active {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        .logout-btn {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            font-size: 15px;
        }

        .logout-btn:hover {
            background: #ef4444;
        }

        .logout-btn i {
            margin-right: 10px;
        }

        .main-content {
            position: relative;
            width: calc(100% - var(--sidebar-width));
            left: var(--sidebar-width);
            min-height: 100vh;
            padding: 30px;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .toggle-btn {
            display: none;
            font-size: 24px;
            color: var(--primary-color);
            background: white;
            border: none;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .toggle-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .footer-admin {
            margin-top: 20px;
            background: white;
            padding: 16px 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            color: #666;
            font-size: 14px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                width: 100%;
                left: 0;
            }

            .main-content.active {
                transform: translateX(20px);
                opacity: 0.6;
            }

            .toggle-btn {
                display: block;
            }

            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 99;
                display: none;
                opacity: 0;
                transition: opacity 0.4s ease;
            }

            .overlay.active {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div class="page-loading" id="pageLoading">
        <div class="page-loading-box">
            <span class="page-loading-spinner"></span>
            <span>Memuat halaman...</span>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <?= $this->include('guru/template/sidebar') ?>

    <div class="main-content" id="mainContent">
        <button class="toggle-btn" id="toggleBtn">
            <i class="fa-solid fa-bars"></i>
        </button>

        <?= $this->renderSection('content') ?>

        <?= $this->include('guru/template/footer') ?>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        const overlay = document.getElementById('overlay');
        const mainContent = document.getElementById('mainContent');
        const pageLoading = document.getElementById('pageLoading');
        let pageLoadingTimeout = null;

        function toggleSidebar() {
            if (sidebar) sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
            if (mainContent) mainContent.classList.toggle('active');
        }

        function showPageLoading() {
            if (pageLoading && !document.body.classList.contains('swal2-shown')) {
                pageLoading.classList.add('active');

                if (pageLoadingTimeout) {
                    clearTimeout(pageLoadingTimeout);
                }

                pageLoadingTimeout = setTimeout(() => {
                    hidePageLoading();
                }, 20000);
            }
        }

        function hidePageLoading() {
            if (pageLoading) {
                pageLoading.classList.remove('active');
            }

            if (pageLoadingTimeout) {
                clearTimeout(pageLoadingTimeout);
                pageLoadingTimeout = null;
            }
        }

        function isFileUploadForm(form) {
            const enctype = (form.getAttribute('enctype') || '').toLowerCase();
            return enctype.includes('multipart/form-data') || !!form.querySelector('input[type="file"]');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]');
            if (!link) return;

            const href = link.getAttribute('href');

            if (!href || href === '#' || href.startsWith('javascript:')) return;
            if (href.startsWith('#')) return;
            if (link.getAttribute('target') === '_blank') return;
            if (link.hasAttribute('download')) return;

            if (
                link.matches('[data-no-loading], .no-loading, [data-swal], .swal-trigger') ||
                href.includes('/export') ||
                href.includes('/download')
            ) {
                return;
            }

            const url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin) return;

            showPageLoading();
        });

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                if (this.matches('[data-no-loading], .no-loading, [data-swal], .swal-form')) {
                    hidePageLoading();
                    return;
                }

                if (isFileUploadForm(this) && !this.hasAttribute('data-force-loading')) {
                    hidePageLoading();
                    return;
                }

                showPageLoading();
            });
        });

        const swalObserver = new MutationObserver(function() {
            const swalVisible = document.querySelector('.swal2-container.swal2-backdrop-show');
            if (swalVisible) {
                hidePageLoading();
            }
        });

        swalObserver.observe(document.body, {
            childList: true,
            subtree: true
        });

        document.addEventListener('DOMContentLoaded', hidePageLoading);
        window.addEventListener('pageshow', hidePageLoading);
        window.addEventListener('focus', hidePageLoading);
        window.addEventListener('load', hidePageLoading);
    </script>

</body>

</html>