<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/bootstrap.php';
$zones=all_zones();
if(is_post()){
    Csrf::verify();
    $name=trim((string)input('full_name'));$email=mb_strtolower(trim((string)input('email')));$phone=trim((string)input('phone'));$institution=trim((string)input('institution'));$zoneId=(int)input('zone_id');$password=(string)input('password');$confirm=(string)input('password_confirmation');
    $errors=[];
    if(mb_strlen($name)<3)$errors[]='Nama penuh diperlukan.';
    if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='E-mel tidak sah.';
    if($institution==='')$errors[]='Nama Kolej Vokasional diperlukan.';
    if($zoneId<1)$errors[]='Sila pilih zon.';
    if(strlen($password)<12)$errors[]='Kata laluan mesti sekurang-kurangnya 12 aksara.';
    if($password!==$confirm)$errors[]='Pengesahan kata laluan tidak sepadan.';
    if(!$errors){
        try{$stmt=db()->prepare("INSERT INTO users(zone_id,role,status,full_name,email,phone,institution,password_hash) VALUES(?,'coach','pending',?,?,?,?,?)");$stmt->execute([$zoneId,$name,$email,$phone?:null,$institution,password_hash($password,PASSWORD_DEFAULT)]);audit((int)db()->lastInsertId(),'register','user',(int)db()->lastInsertId(),['role'=>'coach']);clear_old();flash('success','Pendaftaran diterima. Akaun perlu diluluskan oleh pegawai teknikal sebelum log masuk.');redirect('jurulatih/login.php');}
        catch(PDOException $e){$errors[]=$e->getCode()==='23000'?'E-mel tersebut telah didaftarkan.':'Pendaftaran gagal. Sila cuba lagi.';}
    }
    if($errors) fail_validation($errors,'jurulatih/register.php');
}
$pageTitle='Daftar Jurulatih';require BASE_PATH.'/partials/head.php';$errors=validation_errors();?>
<div class="login-wrapper"><a href="<?= e(url('jurulatih/login.php')) ?>" class="login-back"><i class="fa-solid fa-arrow-left"></i>Kembali</a><img src="<?= e(url('assets/images/Logo KVSkills.png')) ?>" class="login-logo" alt="Logo"><div class="login-card wide-form"><h2>Daftar Jurulatih</h2><div class="login-underline"></div><?php if($errors):?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $er):?><li><?= e($er) ?></li><?php endforeach;?></ul></div><?php endif;?><form method="post"><?= Csrf::field() ?><div class="row g-3"><div class="col-12"><label class="form-label">Nama Penuh</label><input class="form-control" name="full_name" value="<?= e(old('full_name')) ?>" required></div><div class="col-md-6"><label class="form-label">E-mel</label><input type="email" class="form-control" name="email" value="<?= e(old('email')) ?>" required></div><div class="col-md-6"><label class="form-label">Telefon</label><input class="form-control" name="phone" value="<?= e(old('phone')) ?>"></div><div class="col-md-7"><label class="form-label">Kolej Vokasional</label><input class="form-control" name="institution" value="<?= e(old('institution')) ?>" required></div><div class="col-md-5"><label class="form-label">Zon</label><select class="form-select" name="zone_id" required><option value="">Pilih zon</option><?php foreach($zones as $z):?><option value="<?= (int)$z['id'] ?>" <?= selected(old('zone_id'),$z['id']) ?>><?= e($z['name']) ?></option><?php endforeach;?></select></div><div class="col-md-6"><label class="form-label">Kata Laluan</label><input type="password" class="form-control" name="password" minlength="12" required></div><div class="col-md-6"><label class="form-label">Ulang Kata Laluan</label><input type="password" class="form-control" name="password_confirmation" minlength="12" required></div></div><button class="login-btn mt-4" type="submit">Hantar Pendaftaran</button></form></div></div></body></html>
