<?php
declare(strict_types=1);
require __DIR__ . '/soporte.php';

$db = basePrueba();
$controlador = new RegistroController($db);
$sesion = [];
$checks = 0;
function comprobar(bool $condicion, string $mensaje): void
{
    global $checks;
    if (!$condicion) { throw new RuntimeException($mensaje); }
    $checks++;
}
function enviar(string $seccion, array $datos): array
{
    global $controlador, $sesion;
    return $controlador->procesar(['seccion' => $seccion],
        array_merge(['csrf' => $sesion['csrf'], 'accion' => 'guardar', 'id' => ''], $datos), 'POST', $sesion);
}
foreach (['conferencistas', 'conferencias', 'asistentes', 'inscripciones'] as $seccion) {
    $respuesta = $controlador->procesar(['seccion' => $seccion], [], 'GET', $sesion);
    comprobar($respuesta['estado'] === 200, 'No abre la sección.');
    comprobar(str_contains(Vista::renderizar($respuesta['datos']), 'Todavía no hay registros.'), 'No renderiza el listado vacío.');
    comprobar(!isset($respuesta['datos']['modelo']), 'Se entregó un modelo a la vista.');
}
$ponente = ['nombre' => 'María', 'apellido' => 'Pérez', 'especialidad' => 'PHP', 'email' => 'maria@example.com'];
$conferencia = ['nombre' => 'PHP básico', 'fecha' => '2026-12-15', 'ubicacion' => 'Auditorio', 'descripcion' => 'Clases y objetos', 'conferencista_id' => '1'];
$asistente = ['nombre' => '<script>alert(1)</script>', 'apellido' => 'López', 'email' => 'ana@example.com', 'telefono' => '0991234567'];
$inscripcion = ['conferencia_id' => '1', 'asistente_id' => '1'];
foreach (['conferencistas' => $ponente, 'conferencias' => $conferencia, 'asistentes' => $asistente, 'inscripciones' => $inscripcion] as $seccion => $datos) {
    $respuesta = enviar($seccion, $datos);
    comprobar($respuesta['estado'] === 303 && $respuesta['redireccion'] === '?seccion=' . $seccion, 'Fallo al crear y redirigir.');
    $respuesta = $controlador->procesar(['seccion' => $seccion], [], 'GET', $sesion);
    comprobar(count($respuesta['datos']['registros']) === 1, 'No aparece el registro creado.');
    comprobar($respuesta['datos']['mensaje'] !== '', 'No aparece la confirmación.');
    $respuesta = $controlador->procesar(['seccion' => $seccion, 'editar' => '1'], [], 'GET', $sesion);
    comprobar($respuesta['datos']['edicion'] !== null && str_contains(Vista::renderizar($respuesta['datos']), 'Guardar cambios'), 'Fallo al abrir edición.');
    comprobar(enviar($seccion, array_merge($datos, ['id' => '1']))['estado'] === 303, 'Fallo al actualizar.');
}
$respuesta = $controlador->procesar(['seccion' => 'asistentes'], [], 'GET', $sesion);
$html = Vista::renderizar($respuesta['datos']);
comprobar(!str_contains($html, '<script>alert(1)</script>') && str_contains($html, '&lt;script&gt;'), 'La vista no escapa el contenido.');
$respuesta = enviar('asistentes', array_merge($asistente, ['email' => 'inválido']));
comprobar($respuesta['datos']['error'] !== '' && $respuesta['datos']['edicion']['email'] === 'inválido', 'No conserva el formulario inválido.');
$respuesta = enviar('asistentes', ['accion' => 'eliminar', 'id' => '1', 'csrf' => 'incorrecto']);
comprobar(str_contains($respuesta['datos']['error'], 'caducó') && count((new Asistente($db))->listar()) === 1, 'No protege la eliminación por token.');
$respuesta = enviar('asistentes', array_merge($asistente, ['id' => ['1']]));
comprobar(str_contains($respuesta['datos']['error'], 'Identificador'), 'No rechaza un ID mal formado.');
$respuesta = $controlador->procesar(['seccion' => 'asistentes', 'editar' => '999'], [], 'GET', $sesion);
comprobar($respuesta['datos']['edicion'] === null && $respuesta['datos']['error'] !== '', 'No maneja registro inexistente.');
comprobar($controlador->procesar(['seccion' => '../config'], [], 'GET', $sesion)['estado'] === 404, 'No rechaza sección inválida.');
comprobar($controlador->procesar(['seccion' => ['asistentes']], [], 'GET', $sesion)['estado'] === 404, 'No rechaza sección con formato inválido.');
comprobar($controlador->procesar([], [], 'DELETE', $sesion)['estado'] === 405, 'No rechaza método no admitido.');
foreach (['inscripciones', 'conferencias', 'asistentes', 'conferencistas'] as $seccion) {
    comprobar(enviar($seccion, ['accion' => 'eliminar', 'id' => '1'])['estado'] === 303, 'Fallo al eliminar desde el controlador.');
}
echo "OK: $checks comprobaciones del controlador y las vistas con SQLite en memoria.\n";
