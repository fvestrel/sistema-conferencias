<section>
<h2><?= $edicion ? 'Editar registro' : 'Nuevo registro' ?> · <?= e($secciones[$seccion]) ?></h2>
<?php if ($seccion === 'conferencias'): ?><p>Primero registra al conferencista que impartirá la conferencia.</p><?php endif ?>
<?php if ($seccion === 'inscripciones'): ?><p>Primero registra la conferencia y el asistente.</p><?php endif ?>
<form method="post" action="?seccion=<?= e($seccion) ?>" class="campos">
  <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
  <input type="hidden" name="accion" value="guardar">
  <input type="hidden" name="id" value="<?= e($edicion[$clave] ?? '') ?>">
<?php foreach ($campos as $campo => $regla):
    $valor = $edicion[$campo] ?? '';
    if (!is_scalar($valor)) { $valor = ''; }
    $tipo = $regla['type'] ?? 'text'; ?>
  <label><?= e($regla['label']) ?>
  <?php if ($tipo === 'select'): ?>
    <select name="<?= e($campo) ?>" required>
      <option value="">Seleccionar…</option>
      <?php foreach ($opciones[$regla['source']] as $id => $texto): ?>
      <option value="<?= e($id) ?>" <?= (string)$valor === (string)$id ? 'selected' : '' ?>><?= e($texto) ?> (#<?= e($id) ?>)</option>
      <?php endforeach ?>
    </select>
  <?php elseif ($tipo === 'textarea'): ?>
    <textarea name="<?= e($campo) ?>" maxlength="<?= e($regla['max']) ?>" required><?= e($valor) ?></textarea>
  <?php else: ?>
    <input type="<?= e($tipo) ?>" name="<?= e($campo) ?>" value="<?= e($valor) ?>" maxlength="<?= e($regla['max']) ?>" required>
  <?php endif ?>
  </label>
<?php endforeach ?>
<div class="botones"><button type="submit"><?= $edicion ? 'Guardar cambios' : 'Registrar' ?></button>
<?php if ($edicion): ?><a href="?seccion=<?= e($seccion) ?>">Cancelar</a><?php endif ?></div>
</form>
</section>
