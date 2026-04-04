<?php $menu = $menu ?? ''; ?>

<div class="sidebar" id="sidebar">
    <?php
    helper('auth');

    $currentUser = user();

    $userName = $currentUser->username ?? 'User';

    $userRole = 'User';
    if (in_groups('admin')) {
        $userRole = 'Administrator';
    } elseif (in_groups('kepala_sekolah')) {
        $userRole = 'Kepala Sekolah';
    } elseif (in_groups('guru')) {
        $userRole = 'Guru';
    }

    $profilePhoto = !empty($currentUser->profile_photo)
        ? base_url($currentUser->profile_photo)
        : null;

    $userInitial = strtoupper(substr($userName, 0, 1));
    ?>

    <div class="sidebar-header">
        <div class="logo-details">
            <i class="fa-solid fa-book-open-reader"></i>
            <span class="logo_name">TK Kartini</span>
        </div>

        <div class="user-profile-short">
            <?php if ($profilePhoto): ?>
                <img src="<?= esc($profilePhoto) ?>" alt="User Avatar" class="profile-avatar">
            <?php else: ?>
              <div class="profile-avatar profile-fallback p-2 text-white" style="font-weight: bold;">
                    <?= esc($userInitial) ?>
                </div>
            <?php endif; ?>

            <div class="user-name-role">
                <span class="user-name"><?= esc($userName) ?></span>
                <span class="user-role"><?= esc($userRole) ?></span>
            </div>
        </div>
    </div>

    <ul class="nav-links">
        <li>
            <a href="<?= site_url('admin/dashboard') ?>" class="<?= $menu === 'dashboard' ? 'active' : '' ?> nav-loading-link">
                <i class="fa-solid fa-gauge"></i>
                <span class="link_name">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?= site_url('admin/users') ?>" class="<?= $menu === 'users' ? 'active' : '' ?> nav-loading-link">
                <i class="fa-solid fa-users"></i>
                <span class="link_name">Manajemen User</span>
            </a>
        </li>

        <li>
            <a href="<?= site_url('admin/manage-class') ?>"
                class="<?= $menu === 'manage-class' ? 'active' : '' ?> nav-loading-link">
                <i class="fa-solid fa-school"></i>
                <span class="link_name">Kelas dan Periode</span>
            </a>
        </li>

        <li>
            <a href="<?= site_url('admin/siswa') ?>" class="<?= ($menu ?? '') === 'siswa' ? 'active' : '' ?> nav-loading-link">
                <i class="fa-solid fa-user-graduate"></i>
                <span class="link_name">Data Siswa</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a class="logout-btn nav-loading-link" href="<?= site_url('logout') ?>" style="text-decoration: none;">
            <i class="fa-solid fa-right-from-bracket"></i>
            Keluar
        </a>
    </div>
</div>