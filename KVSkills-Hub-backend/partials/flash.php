<?php
$success=flash('success'); $error=flash('error'); $errors=validation_errors();
?>
<?php if ($success): ?><div class="container mt-4"><div class="alert alert-success"><?= e($success) ?></div></div><?php endif; ?>
<?php if ($error): ?><div class="container mt-4"><div class="alert alert-danger"><?= e($error) ?></div></div><?php endif; ?>
<?php if ($errors): ?><div class="container mt-4"><div class="alert alert-danger"><strong>Sila semak maklumat berikut:</strong><ul class="mb-0 mt-2"><?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div></div><?php endif; ?>
