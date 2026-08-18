<?php
declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$competition = null;
$stats = [
    'zones'        => 0,
    'skills'       => 0,
    'participants' => 0,
    'venues'       => 0,
];

try {
    $competition = active_competition();
    $stats['zones']        = (int) db()->query('SELECT COUNT(*) FROM zones WHERE is_active = 1')->fetchColumn();
    $stats['skills']       = (int) db()->query('SELECT COUNT(*) FROM skills WHERE is_active = 1')->fetchColumn();
    $stats['participants'] = (int) db()->query("SELECT COUNT(*) FROM registrations WHERE status = 'approved'")->fetchColumn();
    $stats['venues']       = (int) db()->query('SELECT COUNT(*) FROM venues WHERE is_active = 1')->fetchColumn();
} catch (Throwable $e) {
    $setupError = APP_DEBUG ? $e->getMessage() : 'Pangkalan data belum disediakan.';
}

$pageTitle  = 'Portal Rasmi';
$activePage = 'home';

require BASE_PATH . '/partials/head.php';
require BASE_PATH . '/partials/sidebar.php';
require BASE_PATH . '/partials/flash.php';
?>

<!-- Header Banner -->
<section class="dashboard-header">
    <div class="container text-center">
        <h1>KVSkills Hub Malaysia</h1>
        <p>Portal pengurusan pertandingan kemahiran Kolej Vokasional yang berpusat, selamat dan mesra pengguna.</p>
        
        <?php if ($competition): ?>
            <div class="event-pill">
                <i class="fa-solid fa-calendar-days"></i>
                <?= e(format_date($competition['start_date'])) ?> - <?= e(format_date($competition['end_date'])) ?> · Zon <?= e($competition['host_zone']) ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Error Handling -->
<?php if (isset($setupError)): ?>
    <div class="container my-3">
        <div class="alert alert-warning">
            Sistem memerlukan pemasangan pangkalan data. <?= e($setupError) ?>
        </div>
    </div>
<?php endif; ?>

<!-- Statistik Ringkas -->
<section class="container py-4">
    <div class="row g-3 text-center">
        <?php 
        $statItems = [
            ['zones', 'Zon', 'fa-map'],
            ['skills', 'Bidang', 'fa-screwdriver-wrench'],
            ['venues', 'Lokasi', 'fa-location-dot'],
            ['participants', 'Peserta Diluluskan', 'fa-users'],
        ];
        foreach ($statItems as [$key, $label, $icon]): 
        ?>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <i class="fa-solid <?= e($icon) ?>"></i>
                    <strong><?= (int) $stats[$key] ?></strong>
                    <span><?= e($label) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Menu Navigasi Utama -->
<section class="container py-4">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-5">
            <a href="<?= e(url('maklumat/jadual.php')) ?>" class="dashboard-card">
                <i class="fa-solid fa-calendar-days"></i>
                <h3>Jadual &amp; Penataran</h3>
                <p>Tarikh, masa, pautan Google Meet dan jadual pertandingan setiap bidang.</p>
            </a>
        </div>
        <div class="col-lg-5">
            <a href="<?= e(url('maklumat/lokasi.php')) ?>" class="dashboard-card">
                <i class="fa-solid fa-location-dot"></i>
                <h3>Lokasi Pertandingan</h3>
                <p>9 lokasi rasmi yang menempatkan kesemua 22 bidang.</p>
            </a>
        </div>
        <div class="col-lg-5">
            <a href="<?= e(url('maklumat/peserta.php')) ?>" class="dashboard-card">
                <i class="fa-solid fa-users"></i>
                <h3>Senarai Peserta</h3>
                <p>Carian peserta mengikut zon dan bidang selepas pengesahan.</p>
            </a>
        </div>
        <div class="col-lg-5">
            <a href="<?= e(url('maklumat/keputusan.php')) ?>" class="dashboard-card">
                <i class="fa-solid fa-trophy"></i>
                <h3>Keputusan</h3>
                <p>Keputusan rasmi yang telah diterbitkan oleh pegawai teknikal.</p>
            </a>
        </div>
    </div>
</section>

<!-- Maklumat Peraturan Tambahan -->
<section class="container pb-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h4">Peraturan pembantu peserta</h2>
            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <div class="rule-box">
                        <strong>Beauty Therapy</strong>
                        <span>1 model dan 1 pembantu</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="rule-box">
                        <strong>Cooking</strong>
                        <span>1 pembantu</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="rule-box">
                        <strong>Landscape Gardening</strong>
                        <span>1 pembantu</span>
                    </div>
                </div>
            </div>
            <p class="text-muted mt-3 mb-0">Bidang lain tidak dibenarkan mendaftarkan pembantu atau model.</p>
        </div>
    </div>
</section>

<?php require BASE_PATH . '/partials/footer.php'; ?>