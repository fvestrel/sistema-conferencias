<?php require dirname(__DIR__) . '/layout/encabezado.php'; ?>
<?php if ($mensaje): ?><p class="exito" role="status"><?= e($mensaje) ?></p><?php endif ?>
<?php if ($error): ?><p class="error" role="alert"><?= e($error) ?></p><?php endif ?>
<?php if (!$fatal): ?>
    <?php require __DIR__ . '/formulario.php'; ?>
    <?php require __DIR__ . '/listado.php'; ?>
<?php endif ?>
<?php require dirname(__DIR__) . '/layout/pie.php'; ?>
