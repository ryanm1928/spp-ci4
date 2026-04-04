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
            <a href="<?= site_url('kepala-sekolah/dashboard') ?>" class="<?= $menu === 'dashboard' ? 'active' : '' ?> nav-loading-link">
                <i class="fa-solid fa-gauge"></i>
                <span class="link_name">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?= site_url('kepala-sekolah/laporan') ?>" class="<?= $menu === 'laporan' ? 'active' : '' ?> nav-loading-link">
                <i class="fa-solid fa-file-lines"></i>
                <span class="link_name">Laporan SPP</span>
            </a>

        
    </ul>

    <div class="sidebar-footer">
        <a class="logout-btn nav-loading-link" href="<?= site_url('logout') ?>" style="text-decoration: none;">
            <i class="fa-solid fa-right-from-bracket"></i>
            Keluar
        </a>
    </div>
</div>