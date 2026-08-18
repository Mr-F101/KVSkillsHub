<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';

Auth::requireLogin(['technical', 'admin']);

/**
 * Proses tindakan pendaftaran
 */
if (is_post()) {
    Csrf::verify();

    $id     = (int) input('id');
    $action = (string) input('action');
    $reason = trim((string) input('reason'));

    /**
     * Semak pendaftaran
     */
    $stmt = db()->prepare(
        'SELECT id, status
         FROM registrations
         WHERE id = ?'
    );

    $stmt->execute([$id]);

    $reg = $stmt->fetch();

    if (!$reg) {
        flash('error', 'Pendaftaran tidak ditemui.');
        redirect('pegawai_teknikal/pendaftaran.php');
    }

    /**
     * Lulus pendaftaran
     */
    if ($action === 'approve') {

        $check = db()->prepare(
            "
            SELECT
                s.name,
                s.assistant_count,
                s.model_count,
                SUM(h.helper_type = 'assistant') AS actual_assistants,
                SUM(h.helper_type = 'model') AS actual_models
            FROM registrations r
            JOIN skills s
                ON s.id = r.skill_id
            LEFT JOIN registration_helpers h
                ON h.registration_id = r.id
            WHERE r.id = ?
            GROUP BY
                r.id,
                s.id
            "
        );

        $check->execute([$id]);

        $rules = $check->fetch();

        $invalidAssistantCount =
            !$rules ||
            (int) $rules['assistant_count'] !== (int) $rules['actual_assistants'];

        $invalidModelCount =
            !$rules ||
            (int) $rules['model_count'] !== (int) $rules['actual_models'];

        if ($invalidAssistantCount || $invalidModelCount) {
            flash(
                'error',
                'Pendaftaran tidak mematuhi bilangan pembantu/model bagi bidang tersebut.'
            );

            redirect('pegawai_teknikal/pendaftaran.php');
        }

        $stmt = db()->prepare(
            "
            UPDATE registrations
            SET
                status = 'approved',
                approved_at = NOW(),
                approved_by = ?,
                rejection_reason = NULL
            WHERE id = ?
            "
        );

        $stmt->execute([
            Auth::id(),
            $id
        ]);

        audit(
            Auth::id(),
            'approve',
            'registration',
            $id
        );

        flash(
            'success',
            'Pendaftaran diluluskan.'
        );
    }

    /**
     * Tolak pendaftaran
     */
    elseif ($action === 'reject') {

        if ($reason === '') {
            flash(
                'error',
                'Sebab penolakan diperlukan.'
            );

            redirect('pegawai_teknikal/pendaftaran.php');
        }

        $stmt = db()->prepare(
            "
            UPDATE registrations
            SET
                status = 'rejected',
                rejection_reason = ?,
                approved_at = NULL,
                approved_by = NULL
            WHERE id = ?
            "
        );

        $stmt->execute([
            $reason,
            $id
        ]);

        audit(
            Auth::id(),
            'reject',
            'registration',
            $id,
            [
                'reason' => $reason
            ]
        );

        flash(
            'success',
            'Pendaftaran ditolak.'
        );
    }

    redirect('pegawai_teknikal/pendaftaran.php');
}

/**
 * Filter status
 */
$allowedStatuses = [
    'submitted',
    'approved',
    'rejected'
];

$status = $_GET['status'] ?? 'submitted';

if (!in_array($status, $allowedStatuses, true)) {
    $status = 'submitted';
}

/**
 * Filter bidang
 */
$skillId = (int) ($_GET['skill_id'] ?? 0);

$skills = all_skills();

/**
 * Ambil senarai pendaftaran
 */
$sql = "
    SELECT
        r.*,
        s.name AS skill_name,
        z.name AS zone_name,
        u.full_name AS coach_name,
        GROUP_CONCAT(
            CONCAT(
                h.helper_type,
                ': ',
                h.full_name
            )
            SEPARATOR '||'
        ) AS helpers

    FROM registrations r

    JOIN skills s
        ON s.id = r.skill_id

    JOIN zones z
        ON z.id = r.zone_id

    JOIN users u
        ON u.id = r.coach_user_id

    LEFT JOIN registration_helpers h
        ON h.registration_id = r.id

    WHERE r.status = ?
";

$params = [
    $status
];

/**
 * Filter berdasarkan bidang
 */
if ($skillId) {
    $sql .= "
        AND r.skill_id = ?
    ";

    $params[] = $skillId;
}

$sql .= "
    GROUP BY r.id
    ORDER BY
        s.sort_order,
        z.sort_order
";

$stmt = db()->prepare($sql);

$stmt->execute($params);

$rows = $stmt->fetchAll();

/**
 * Page settings
 */
$pageTitle  = 'Semakan Pendaftaran';
$activePage = 'technical';

require BASE_PATH . '/partials/head.php';
require BASE_PATH . '/partials/sidebar.php';
require BASE_PATH . '/partials/flash.php';

?>

<!-- =========================
     HEADER
========================= -->

<section class="dashboard-header">

    <div class="container text-center">

        <h1>
            Semakan Pendaftaran
        </h1>

        <p>
            Sahkan peserta, pembantu dan model
            mengikut peraturan bidang.
        </p>

    </div>

</section>


<!-- =========================
     CONTENT
========================= -->

<section class="container py-4">

    <!-- FILTER -->

    <form
        method="get"
        class="card card-body mb-4"
    >

        <div class="row g-3">

            <!-- STATUS -->

            <div class="col-md-5">

                <select
                    name="status"
                    class="form-select"
                >

                    <option
                        value="submitted"
                        <?= selected($status, 'submitted') ?>
                    >
                        Menunggu
                    </option>

                    <option
                        value="approved"
                        <?= selected($status, 'approved') ?>
                    >
                        Diluluskan
                    </option>

                    <option
                        value="rejected"
                        <?= selected($status, 'rejected') ?>
                    >
                        Ditolak
                    </option>

                </select>

            </div>


            <!-- BIDANG -->

            <div class="col-md-5">

                <select
                    name="skill_id"
                    class="form-select"
                >

                    <option value="0">
                        Semua bidang
                    </option>

                    <?php foreach ($skills as $skill): ?>

                        <option
                            value="<?= (int) $skill['id'] ?>"
                            <?= selected($skillId, $skill['id']) ?>
                        >
                            <?= e($skill['name']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- BUTTON FILTER -->

            <div class="col-md-2">

                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    Tapis
                </button>

            </div>

        </div>

    </form>


    <!-- TABLE -->

    <div class="table-responsive">

        <table class="table table-hover">

            <thead>

                <tr>

                    <th>
                        Peserta
                    </th>

                    <th>
                        Zon / Bidang
                    </th>

                    <th>
                        Jurulatih
                    </th>

                    <th>
                        Pembantu / Model
                    </th>

                    <th>
                        Tindakan
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php foreach ($rows as $row): ?>

                    <tr>

                        <!-- PESERTA -->

                        <td>

                            <?= e($row['participant_name']) ?>

                            <br>

                            <small>
                                <?= e($row['institution']) ?>
                            </small>

                        </td>


                        <!-- ZON / BIDANG -->

                        <td>

                            <?= e($row['zone_name']) ?>

                            <br>

                            <small>
                                <?= e($row['skill_name']) ?>
                            </small>

                        </td>


                        <!-- JURULATIH -->

                        <td>

                            <?= e($row['coach_name']) ?>

                        </td>


                        <!-- PEMBANTU / MODEL -->

                        <td>

                            <?php
                            $helpers = array_filter(
                                explode(
                                    '||',
                                    (string) $row['helpers']
                                )
                            );
                            ?>

                            <?php if ($helpers): ?>

                                <?php foreach ($helpers as $helper): ?>

                                    <div>
                                        <?= e($helper) ?>
                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <span class="text-muted">
                                    Tiada
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- TINDAKAN -->

                        <td>

                            <?php if ($row['status'] === 'submitted'): ?>

                                <div class="d-grid gap-2">

                                    <!-- LULUS -->

                                    <form method="post">

                                        <?= Csrf::field() ?>

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $row['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="approve"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-success w-100"
                                        >
                                            Lulus
                                        </button>

                                    </form>


                                    <!-- TOLAK -->

                                    <form
                                        method="post"
                                        class="d-flex gap-1"
                                    >

                                        <?= Csrf::field() ?>

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $row['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="reject"
                                        >

                                        <input
                                            type="text"
                                            name="reason"
                                            class="form-control form-control-sm"
                                            placeholder="Sebab"
                                            required
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            Tolak
                                        </button>

                                    </form>

                                </div>


                            <?php else: ?>

                                <span
                                    class="status status-<?= e($row['status']) ?>"
                                >
                                    <?= e(ucfirst($row['status'])) ?>
                                </span>


                                <?php if ($row['rejection_reason']): ?>

                                    <small class="d-block text-danger">

                                        <?= e(
                                            $row['rejection_reason']
                                        ) ?>

                                    </small>

                                <?php endif; ?>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <!-- TIADA REKOD -->

                <?php if (!$rows): ?>

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted"
                        >
                            Tiada rekod.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<?php

require BASE_PATH . '/partials/footer.php';

?>