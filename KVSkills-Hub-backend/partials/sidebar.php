<?php
$activePage = $activePage ?? '';
$user = Auth::user();
$links = [
    ['home','index.php','fa-house','Maklumat KVSkills'],
    ['jadual','maklumat/jadual.php','fa-calendar-days','Jadual & Dokumen'],
    ['lokasi','maklumat/lokasi.php','fa-location-dot','Lokasi Pertandingan'],
    ['peserta','maklumat/peserta.php','fa-users','Senarai Peserta'],
    ['keputusan','maklumat/keputusan.php','fa-trophy','Keputusan'],
    ['logo','maklumat/logo.php','fa-download','Muat Turun Logo'],
];
if ($user && $user['role']==='coach') $links[]=['coach','jurulatih/dashboard.php','fa-user-group','Dashboard Jurulatih'];
if ($user && in_array($user['role'],['technical','admin'],true)) $links[]=['technical','pegawai_teknikal/dashboard.php','fa-user-gear','Dashboard Teknikal'];
if ($user) $links[]=['password','auth/password.php','fa-key','Tukar Kata Laluan'];
?>
<div class="sidebar-topbar">
    <a href="<?= e(url('index.php')) ?>" class="sidebar-topbar-brand">KVSkills Hub</a>
    <button class="sidebar-toggle" type="button" aria-label="Buka menu"><i class="fa-solid fa-bars"></i></button>
</div>
<div class="sidebar-overlay"></div>
<aside class="sidebar">
    <button class="sidebar-close-btn" type="button" aria-label="Tutup sidebar"><i class="fa-solid fa-xmark"></i></button>
    <a href="<?= e(url('index.php')) ?>" class="sidebar-brand">
        <img src="<?= e(url('assets/images/Logo KVSkills.png')) ?>" alt="Logo KVSkills">
        <span>KVSkills Hub</span>
    </a>
    <ul class="sidebar-nav">
        <?php foreach ($links as [$key,$href,$icon,$label]): ?>
        <li><a href="<?= e(url($href)) ?>" class="sidebar-link <?= $activePage===$key?'active':'' ?>">
            <i class="fa-solid <?= e($icon) ?>"></i><span><?= e($label) ?></span>
        </a></li>
        <?php endforeach; ?>
        <li>
            <?php if ($user): ?>
            <form action="<?= e(url('auth/logout.php')) ?>" method="post" class="m-0">
                <?= Csrf::field() ?>
                <button class="sidebar-link sidebar-button" type="submit"><i class="fa-solid fa-right-from-bracket"></i><span>Log Keluar</span></button>
            </form>
            <?php else: ?>
            <a href="<?= e(url('jawatan.php')) ?>" class="sidebar-link"><i class="fa-solid fa-right-to-bracket"></i><span>Log Masuk</span></a>
            <?php endif; ?>
        </li>
    </ul>
</aside>
<button class="sidebar-reopen-btn" type="button" aria-label="Buka sidebar"><i class="fa-solid fa-bars"></i></button>
<div class="main-content">
