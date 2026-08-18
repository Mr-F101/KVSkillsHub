<?php
declare(strict_types=1);
require_once __DIR__ . '/config/bootstrap.php';
$pageTitle='Pilih Peranan';
require BASE_PATH.'/partials/head.php';
?>
<section class="hero"><div class="container text-center">
<img src="<?= e(url('assets/images/Logo KVSkills.png')) ?>" class="logo mb-4 fade-in-item" alt="Logo">
<h1 class="title fade-in-item">KVSkills Hub</h1><p class="subtitle fade-in-item">Pertandingan Kemahiran Kolej Vokasional Malaysia</p>
<div class="row justify-content-center mt-5">
<div class="col-lg-4 col-md-6 mb-4 fade-in-item"><a href="<?= e(url('jurulatih/login.php')) ?>" class="menu-btn"><i class="fa-solid fa-user-group"></i>Jurulatih</a></div>
<div class="col-lg-4 col-md-6 mb-4 fade-in-item"><a href="<?= e(url('pegawai_teknikal/login.php')) ?>" class="menu-btn"><i class="fa-solid fa-user-gear"></i>Pegawai Teknikal</a></div>
</div><a href="<?= e(url('index.php')) ?>" class="btn btn-outline-secondary">Kembali ke portal awam</a>
</div></section>
</body></html>
