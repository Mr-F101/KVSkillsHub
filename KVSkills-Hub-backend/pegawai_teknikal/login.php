<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/bootstrap.php';
if(Auth::check()) redirect((Auth::user()['role']==='coach')?'jurulatih/dashboard.php':'pegawai_teknikal/dashboard.php');
if(is_post()){
    Csrf::verify();
    if(Auth::attempt(trim((string)input('email')),(string)input('password'),['technical','admin'])){flash('success','Selamat datang ke dashboard pegawai teknikal.');redirect('pegawai_teknikal/dashboard.php');}
    flash('error','Log masuk gagal. Sila semak e-mel, kata laluan dan status akaun.');
}
$pageTitle='Log Masuk Pegawai Teknikal';require BASE_PATH.'/partials/head.php';?>
<div class="login-wrapper"><a href="<?= e(url('jawatan.php')) ?>" class="login-back"><i class="fa-solid fa-arrow-left"></i>Kembali</a><img src="<?= e(url('assets/images/Logo KVSkills.png')) ?>" class="login-logo" alt="Logo"><div class="login-brand">KVSKILLS HUB</div><div class="login-card"><h2>Log Masuk Teknikal</h2><div class="login-underline"></div><?php if($m=flash('error')):?><div class="alert alert-danger"><?= e($m) ?></div><?php endif;?><form method="post"><?= Csrf::field() ?><div class="mb-3"><label>E-mel</label><input type="email" class="form-control" name="email" required autocomplete="email"></div><div class="mb-3"><label>Kata Laluan</label><input type="password" class="form-control" name="password" required autocomplete="current-password"></div><button class="login-btn" type="submit">Log Masuk</button><p class="text-center mt-3 mb-0"><a href="<?= e(url('pegawai_teknikal/register.php')) ?>">Pendaftaran pegawai berautoriti</a></p></form></div></div></body></html>
