<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/bootstrap.php';
$competition=active_competition();
$sql="SELECT v.id,v.code,v.name,GROUP_CONCAT(DISTINCT s.name ORDER BY s.sort_order SEPARATOR '||') AS skills FROM venues v LEFT JOIN competition_skills cs ON cs.venue_id=v.id AND cs.competition_id=? LEFT JOIN skills s ON s.id=cs.skill_id WHERE v.is_active=1 GROUP BY v.id ORDER BY v.code";
$stmt=db()->prepare($sql);$stmt->execute([(int)($competition['id']??0)]);$venues=$stmt->fetchAll();
$pageTitle='Lokasi Pertandingan'; $activePage='lokasi'; require BASE_PATH.'/partials/head.php'; require BASE_PATH.'/partials/sidebar.php';
?>
<section class="dashboard-header"><div class="container text-center"><h1>Lokasi Pertandingan</h1><p>Lokasi rasmi KVSkills Kebangsaan 2025 di Zon Melaka/Negeri Sembilan.</p></div></section>
<section class="container py-5"><div class="row g-4">
<?php foreach($venues as $venue): $items=array_filter(explode('||',(string)$venue['skills'])); $mapsUrl='https://www.google.com/maps/dir/?api=1&destination='.rawurlencode((string)$venue['name']); ?>
<div class="col-lg-6"><a href="<?= e($mapsUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-reset text-decoration-none d-block h-100" aria-label="Dapatkan arah ke <?= e($venue['name']) ?> melalui Google Maps"><div class="card lokasi-card shadow-sm h-100"><div class="card-body"><div class="d-flex align-items-start gap-3"><div class="venue-code"><?= e($venue['code']) ?></div><div><h3 class="h5 mb-2"><?= e($venue['name']) ?></h3><span class="badge text-bg-light mb-3"><?= count($items) ?> bidang</span><ul class="mb-0"><?php foreach($items as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></div></div></div></div></a></div>
<?php endforeach; ?></div></section>
<?php require BASE_PATH.'/partials/footer.php'; ?>
