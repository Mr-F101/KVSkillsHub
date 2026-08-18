<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/bootstrap.php';
$level=(string)($_GET['level']??'zone');
if(!in_array($level,['zone','national'],true)) $level='zone';
$skillId=(int)($_GET['skill_id']??0); $skills=all_skills();
$sql='SELECT b.*,s.name AS skill_name,s.sort_order FROM briefings b JOIN skills s ON s.id=b.skill_id WHERE b.is_published=1 AND b.level=?'; $params=[$level];
if($skillId){$sql.=' AND b.skill_id=?';$params[]=$skillId;} $sql.=' ORDER BY b.briefing_date,b.start_time,s.sort_order';
$stmt=db()->prepare($sql);$stmt->execute($params);$briefings=$stmt->fetchAll();
$docRows=db()->query("SELECT s.id AS skill_id,s.name,s.sort_order,d.id AS document_id,d.original_filename FROM skills s LEFT JOIN documents d ON d.skill_id=s.id AND d.category='national_schedule' AND d.visibility='public' AND d.is_active=1 WHERE s.is_active=1 ORDER BY s.sort_order")->fetchAll();
$generalDocs=public_documents();
$pageTitle='Jadual & Dokumen'; $activePage='jadual'; require BASE_PATH.'/partials/head.php'; require BASE_PATH.'/partials/sidebar.php';
?>
<section class="dashboard-header"><div class="container text-center"><h1>Jadual & Dokumen KVSkills</h1><p>Data penataran dinormalkan daripada fail rasmi yang dibekalkan.</p></div></section>
<section class="container py-4">
<form class="card card-body shadow-sm mb-4" method="get"><div class="row g-3 align-items-end"><div class="col-md-4"><label class="form-label">Peringkat Penataran</label><select name="level" class="form-select"><option value="zone" <?= selected($level,'zone') ?>>Zon</option><option value="national" <?= selected($level,'national') ?>>Kebangsaan</option></select></div><div class="col-md-5"><label class="form-label">Bidang</label><select name="skill_id" class="form-select"><option value="0">Semua bidang</option><?php foreach($skills as $s): ?><option value="<?= (int)$s['id'] ?>" <?= selected($skillId,$s['id']) ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div><div class="col-md-3"><button class="btn btn-primary w-100">Tapis Jadual</button></div></div></form>
<div class="card shadow-sm mb-5"><div class="card-body"><h2 class="h4">Penataran Peringkat <?= $level==='zone'?'Zon':'Kebangsaan' ?></h2><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Bidang</th><th>Tarikh</th><th>Masa</th><th>Pautan</th></tr></thead><tbody><?php foreach($briefings as $b): ?><tr><td><?= e($b['skill_name']) ?></td><td><?= e(format_date($b['briefing_date'])) ?></td><td><?= e(format_time($b['start_time'])) ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?= e($b['meeting_url']) ?>" target="_blank" rel="noopener noreferrer">Google Meet</a></td></tr><?php endforeach; ?><?php if(!$briefings): ?><tr><td colspan="4" class="text-center text-muted">Tiada rekod.</td></tr><?php endif; ?></tbody></table></div></div></div>
<h2 class="h4 mb-3">Jadual Pertandingan Kebangsaan Mengikut Bidang</h2><div class="row g-3 mb-5"><?php foreach($docRows as $d): ?><div class="col-md-6 col-xl-4"><div class="document-card"><div><strong><?= e($d['name']) ?></strong><small>6 - 11 September 2025</small></div><?php if($d['document_id']): ?><a class="btn btn-sm btn-primary" href="<?= e(url('download.php?id='.(int)$d['document_id'])) ?>"><i class="fa-solid fa-download"></i> Muat turun</a><?php else: ?><span class="badge text-bg-secondary">Fail belum dibekalkan</span><?php endif; ?></div></div><?php endforeach; ?></div>
<h2 class="h4 mb-3">Dokumen Rasmi Lain</h2><div class="row g-3"><?php foreach($generalDocs as $d): if(in_array($d['category'],['national_schedule','national_schedule_source'],true)) continue; ?><div class="col-md-6"><div class="document-card"><div><strong><?= e($d['title']) ?></strong><small><?= number_format((int)$d['file_size']/1024,0) ?> KB</small></div><a class="btn btn-sm btn-outline-primary" href="<?= e(url('download.php?id='.(int)$d['id'])) ?>">Muat turun</a></div></div><?php endforeach; ?></div>
</section>
<?php require BASE_PATH.'/partials/footer.php'; ?>
