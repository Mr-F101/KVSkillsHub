<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/bootstrap.php';
$q=trim((string)($_GET['q']??''));$skillId=(int)($_GET['skill_id']??0);$zoneId=(int)($_GET['zone_id']??0);
$skills=all_skills();$zones=all_zones();
$sql="SELECT r.id,r.participant_name,r.institution,s.name AS skill_name,z.name AS zone_name,u.full_name AS coach_name FROM registrations r JOIN skills s ON s.id=r.skill_id JOIN zones z ON z.id=r.zone_id JOIN users u ON u.id=r.coach_user_id WHERE r.status='approved'";$params=[];
if($q!==''){$sql.=' AND (r.participant_name LIKE ? OR r.institution LIKE ? OR u.full_name LIKE ?)';$like='%'.$q.'%';array_push($params,$like,$like,$like);}if($skillId){$sql.=' AND r.skill_id=?';$params[]=$skillId;}if($zoneId){$sql.=' AND r.zone_id=?';$params[]=$zoneId;}$sql.=' ORDER BY s.sort_order,z.sort_order,r.participant_name';
$stmt=db()->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
$pageTitle='Senarai Peserta';$activePage='peserta';require BASE_PATH.'/partials/head.php';require BASE_PATH.'/partials/sidebar.php';
?>
<section class="dashboard-header"><div class="container text-center"><h1>Senarai Peserta KVSkills</h1><p>Hanya pendaftaran yang telah disahkan dipaparkan kepada umum.</p></div></section>
<section class="container py-4"><form method="get" class="card card-body shadow-sm"><div class="row g-3"><div class="col-lg-5"><input name="q" value="<?= e($q) ?>" class="form-control" placeholder="Cari peserta, kolej atau jurulatih"></div><div class="col-lg-3"><select name="skill_id" class="form-select"><option value="0">Semua bidang</option><?php foreach($skills as $s): ?><option value="<?= (int)$s['id'] ?>" <?= selected($skillId,$s['id']) ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div><div class="col-lg-2"><select name="zone_id" class="form-select"><option value="0">Semua zon</option><?php foreach($zones as $z): ?><option value="<?= (int)$z['id'] ?>" <?= selected($zoneId,$z['id']) ?>><?= e($z['name']) ?></option><?php endforeach; ?></select></div><div class="col-lg-2"><button class="btn btn-primary w-100">Cari</button></div></div></form>
<div class="table-responsive mt-4"><table class="table table-striped table-hover"><thead><tr><th>Bil.</th><th>Nama Peserta</th><th>Kolej</th><th>Zon</th><th>Bidang</th><th>Jurulatih</th></tr></thead><tbody><?php foreach($rows as $i=>$r): ?><tr><td><?= $i+1 ?></td><td><?= e($r['participant_name']) ?></td><td><?= e($r['institution']) ?></td><td><?= e($r['zone_name']) ?></td><td><?= e($r['skill_name']) ?></td><td><?= e($r['coach_name']) ?></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="6" class="text-center text-muted py-4">Tiada peserta ditemui.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require BASE_PATH.'/partials/footer.php'; ?>
