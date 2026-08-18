<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/bootstrap.php';
if(Auth::check()) redirect((Auth::user()['role']==='coach')?'jurulatih/dashboard.php':'pegawai_teknikal/dashboard.php');
if(is_post()){
    Csrf::verify();
    $email=trim((string)input('email'));$password=(string)input('password');
    if(Auth::attempt($email,$password,'coach')){flash('success','Selamat datang ke dashboard jurulatih.');redirect('jurulatih/dashboard.php');}
    flash('error','Log masuk gagal. Akaun mungkin belum diluluskan atau maklumat tidak tepat.');
}
$pageTitle='Log Masuk Jurulatih';require BASE_PATH.'/partials/head.php';?>
<div class="login-wrapper"><a href="<?= e(url('jawatan.php')) ?>" class="login-back"><i class="fa-solid fa-arrow-left"></i>Kembali</a><img src="<?= e(url('assets/images/Logo KVSkills.png')) ?>" class="login-logo" alt="Logo"><div class="login-brand">KVSKILLS HUB</div><div class="login-card"><h2>Log Masuk Jurulatih</h2><div class="login-underline"></div><?php if($m=flash('error')):?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?><form method="post"><?= Csrf::field() ?><div class="mb-3"><label for="email">E-mel</label><div class="input-group login-input-group"><span class="input-group-text"><i class="fa-solid fa-envelope"></i></span><input type="email" class="form-control" id="email" name="email" required autocomplete="email"></div></div><div class="mb-3"><label for="password">Kata Laluan</label><div class="input-group login-input-group"><span class="input-group-text"><i class="fa-solid fa-lock"></i></span><input type="password" class="form-control" id="password" name="password" required autocomplete="current-password"></div></div><button class="login-btn" type="submit">Log Masuk</button><p class="text-center mt-3 mb-0">Belum mempunyai akaun? <a href="<?= e(url('jurulatih/register.php')) ?>">Daftar di sini</a></p></form></div></div></body></html>
