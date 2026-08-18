<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/bootstrap.php';
require_once BASE_PATH.'/config/upload.php';
Auth::requireLogin(['technical','admin']);
$skills=all_skills();

if(is_post()){
    Csrf::verify();
    $action=(string)input('action','upload');
    if($action==='disable'){
        $id=(int)input('id');
        db()->prepare('UPDATE documents SET is_active=0 WHERE id=?')->execute([$id]);
        audit(Auth::id(),'disable','document',$id);
        flash('success','Dokumen dinyahaktifkan.');
        redirect('pegawai_teknikal/dokumen.php');
    }

    $stored=null;
    try{
        $title=trim((string)input('title'));
        $category=(string)input('category');
        $skillId=(int)input('skill_id')?:null;
        $visibility=(string)input('visibility');
        if($title==='' || !in_array($category,['official_letter','briefing','national_schedule','question','result','other'],true) || !in_array($visibility,['public','authenticated','private'],true)){
            throw new RuntimeException('Maklumat dokumen tidak lengkap.');
        }
        $stored=store_document_upload($_FILES['document']??[],[
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
        $stmt=db()->prepare('INSERT INTO documents(skill_id,uploaded_by,title,category,file_path,original_filename,mime_type,file_size,checksum_sha256,visibility,is_active) VALUES(?,?,?,?,?,?,?,?,?,?,1)');
        $stmt->execute([$skillId,Auth::id(),$title,$category,$stored['relative_path'],$stored['original_filename'],$stored['mime_type'],$stored['file_size'],$stored['checksum_sha256'],$visibility]);
        audit(Auth::id(),'upload','document',(int)db()->lastInsertId());
        flash('success','Dokumen berjaya dimuat naik.');
    }catch(Throwable $e){
        if($stored && isset($stored['relative_path'])){
            $orphan=BASE_PATH.'/'.ltrim((string)$stored['relative_path'],'/');
            if(is_file($orphan))@unlink($orphan);
        }
        flash('error',$e->getMessage());
    }
    redirect('pegawai_teknikal/dokumen.php');
}
$docs=db()->query('SELECT d.*,s.name skill_name,u.full_name uploader FROM documents d LEFT JOIN skills s ON s.id=d.skill_id LEFT JOIN users u ON u.id=d.uploaded_by ORDER BY d.created_at DESC')->fetchAll();$pageTitle='Pengurusan Dokumen';$activePage='technical';require BASE_PATH.'/partials/head.php';require BASE_PATH.'/partials/sidebar.php';require BASE_PATH.'/partials/flash.php';?>
<section class="dashboard-header"><div class="container text-center"><h1>Pengurusan Dokumen</h1><p>Muat naik PDF, DOCX atau XLSX sehingga <?= (int)env('MAX_UPLOAD_MB',10) ?> MB.</p></div></section><section class="container py-4"><div class="card card-body shadow-sm mb-4"><h2 class="h5">Muat Naik Dokumen</h2><form method="post" enctype="multipart/form-data"><?= Csrf::field() ?><input type="hidden" name="action" value="upload"><div class="row g-3"><div class="col-md-6"><label class="form-label">Tajuk</label><input name="title" class="form-control" required></div><div class="col-md-3"><label class="form-label">Kategori</label><select name="category" class="form-select"><option value="national_schedule">Jadual Kebangsaan</option><option value="briefing">Penataran</option><option value="official_letter">Surat Rasmi</option><option value="question">Soalan</option><option value="result">Keputusan</option><option value="other">Lain-lain</option></select></div><div class="col-md-3"><label class="form-label">Akses</label><select name="visibility" class="form-select"><option value="public">Awam</option><option value="authenticated">Pengguna Log Masuk</option><option value="private">Teknikal Sahaja</option></select></div><div class="col-md-6"><label class="form-label">Bidang (opsyenal)</label><select name="skill_id" class="form-select"><option value="0">Umum</option><?php foreach($skills as $s):?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach;?></select></div><div class="col-md-6"><label class="form-label">Fail</label><input type="file" name="document" class="form-control" accept=".pdf,.docx,.xlsx" required></div></div><button class="btn btn-primary mt-3">Muat Naik</button></form></div><div class="table-responsive"><table class="table table-hover"><thead><tr><th>Dokumen</th><th>Kategori/Bidang</th><th>Akses</th><th>Status</th><th>Tindakan</th></tr></thead><tbody><?php foreach($docs as $d):?><tr><td><?= e($d['title']) ?><br><small><?= e($d['original_filename']) ?></small></td><td><?= e($d['category']) ?><br><small><?= e($d['skill_name']??'Umum') ?></small></td><td><?= e($d['visibility']) ?></td><td><?= $d['is_active']?'Aktif':'Tidak aktif' ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?= e(url('download.php?id='.(int)$d['id'])) ?>">Muat turun</a><?php if($d['is_active']):?><form method="post" class="d-inline"><?= Csrf::field() ?><input type="hidden" name="action" value="disable"><input type="hidden" name="id" value="<?= (int)$d['id'] ?>"><button class="btn btn-sm btn-outline-danger">Nyahaktif</button></form><?php endif;?></td></tr><?php endforeach;?></tbody></table></div></section><?php require BASE_PATH.'/partials/footer.php';?>
