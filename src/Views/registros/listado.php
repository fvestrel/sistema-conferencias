<section><h2>Listado de <?= e(strtolower($secciones[$seccion])) ?> <small>(<?= count($registros) ?>)</small></h2>
<?php if (!$registros): ?><p>Todavía no hay registros. Completa el formulario para agregar el primero.</p>
<?php else: ?>
<div class="tabla"><table><thead><tr><th>ID</th>
<?php foreach ($campos as $regla): ?><th><?= e($regla['label']) ?></th><?php endforeach ?>
<?php if ($seccion === 'inscripciones'): ?><th>Fecha de inscripción</th><?php endif ?>
<th>Acciones</th></tr></thead><tbody>
<?php foreach ($registros as $fila): ?><tr><td><?= e($fila[$clave]) ?></td>
<?php foreach ($campos as $campo => $regla): ?>
<td><?= e(isset($regla['source']) ? ($opciones[$regla['source']][$fila[$campo]] ?? 'No disponible') : $fila[$campo]) ?></td>
<?php endforeach ?>
<?php if ($seccion === 'inscripciones'): ?><td><?= e($fila['fecha_inscripcion']) ?></td><?php endif ?>
<td><div class="acciones"><a href="?seccion=<?= e($seccion) ?>&amp;editar=<?= e($fila[$clave]) ?>">Editar</a>
<form method="post" action="?seccion=<?= e($seccion) ?>" onsubmit="return confirm('¿Eliminar este registro?');">
<input type="hidden" name="csrf" value="<?= e($csrf) ?>">
<input type="hidden" name="accion" value="eliminar">
<input type="hidden" name="id" value="<?= e($fila[$clave]) ?>">
<button class="eliminar" type="submit">Eliminar</button></form></div></td></tr>
<?php endforeach ?>
</tbody></table></div><?php endif ?></section>
