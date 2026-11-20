<?php
declare(strict_types=1);
// Prueba aislada: no toca bases de datos ni registros del usuario.
require __DIR__ . '/soporte.php';
$db = basePrueba();
$checks = 0;
function verificar(bool $condicion, string $mensaje): void {
    global $checks;
    if (!$condicion) { throw new RuntimeException($mensaje); }
    $checks++;
}
function rechazar(callable $operacion, string $clase): void {
    try { $operacion(); } catch (Throwable $ex) {
        verificar($ex instanceof $clase, 'Excepción inesperada: ' . get_class($ex));
        return;
    }
    throw new RuntimeException('Se aceptó una operación que debía rechazarse.');
}
$p = new Conferencista($db); $c = new Conferencia($db);
$a = new Asistente($db); $i = new Inscripcion($db);
$conferencista = ['nombre' => 'María', 'apellido' => 'Pérez', 'especialidad' => 'POO', 'email' => 'maria@example.com'];
$asistente = ['nombre' => "Ana O'Connor", 'apellido' => 'López', 'email' => 'ana@example.com', 'telefono' => '0991234567'];
$conferencia = ['conferencista_id' => '1', 'nombre' => 'Introducción a PHP', 'fecha' => '2026-12-15', 'ubicacion' => 'Auditorio', 'descripcion' => 'Clases y objetos'];
$p->guardar($conferencista); $a->guardar($asistente); $c->guardar($conferencia);
$i->guardar(['conferencia_id' => '1', 'asistente_id' => '1']);
foreach ([$p, $a, $c, $i] as $modelo) {
    verificar(count($modelo->listar()) === 1, 'Fallo al crear o listar.');
    verificar($modelo->buscar(1) !== null, 'Fallo al consultar.');
}
verificar($a->buscar(1)['nombre'] === "Ana O'Connor", 'No conserva apóstrofes.');
$conferencista['especialidad'] = 'Bases de datos'; $p->guardar($conferencista, 1);
$asistente['telefono'] = '+593 991234567'; $a->guardar($asistente, 1);
$conferencia['nombre'] = 'PHP y MySQL'; $c->guardar($conferencia, 1);
$a->guardar(array_merge($asistente, ['nombre' => 'Luis', 'email' => 'luis@example.com']));
$i->guardar(['conferencia_id' => '1', 'asistente_id' => '2'], 1);
verificar($p->buscar(1)['especialidad'] === 'Bases de datos', 'Fallo al editar conferencista.');
verificar($a->buscar(1)['telefono'] === '+593 991234567', 'Fallo al editar asistente.');
verificar($c->buscar(1)['nombre'] === 'PHP y MySQL', 'Fallo al editar conferencia.');
verificar((int)$i->buscar(1)['asistente_id'] === 2, 'Fallo al editar inscripción.');
rechazar(fn() => $i->guardar(['conferencia_id' => '1', 'asistente_id' => '2']), PDOException::class);
rechazar(fn() => $i->guardar(['conferencia_id' => '999', 'asistente_id' => '2']), PDOException::class);
rechazar(fn() => $p->eliminar(1), PDOException::class);
rechazar(fn() => $c->eliminar(1), PDOException::class);
rechazar(fn() => $a->eliminar(2), PDOException::class);
rechazar(fn() => $a->guardar(array_merge($asistente, ['email' => 'incorrecto'])), InvalidArgumentException::class);
rechazar(fn() => $c->guardar(array_merge($conferencia, ['fecha' => '2026-02-30'])), InvalidArgumentException::class);
rechazar(fn() => $a->guardar(array_merge($asistente, ['nombre' => '  '])), InvalidArgumentException::class);
rechazar(fn() => $a->guardar(array_merge($asistente, ['nombre' => ['inválido']])), InvalidArgumentException::class);
rechazar(fn() => $a->guardar($asistente, 999), InvalidArgumentException::class);
$i->eliminar(1); $c->eliminar(1); $a->eliminar(1); $a->eliminar(2); $p->eliminar(1);
foreach ([$p, $a, $c, $i] as $modelo) {
    verificar(count($modelo->listar()) === 0, 'Fallo al eliminar.');
}
echo "OK: $checks comprobaciones. Prueba aislada con SQLite; falta validar instalación con MySQL/MariaDB.\n";
