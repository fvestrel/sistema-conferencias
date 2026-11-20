<?php
declare(strict_types=1);


session_start();
require dirname(__DIR__) . '/src/bootstrap.php';

try {
    $db = Database::connect(require dirname(__DIR__) . '/config/database.php');
} catch (PDOException $ex) {
    error_log((string)$ex);
    $db = null;
}

$controlador = new RegistroController($db);
$respuesta = $controlador->procesar($_GET, $_POST, $_SERVER['REQUEST_METHOD'], $_SESSION);
http_response_code($respuesta['estado']);

if (isset($respuesta['redireccion'])) {
    header('Location: ' . $respuesta['redireccion']);
    exit;
}
if (isset($respuesta['texto'])) {
    header('Content-Type: text/plain; charset=utf-8');
    if ($respuesta['estado'] === 405) { header('Allow: GET, POST'); }
    echo $respuesta['texto'];
    exit;
}
header('Content-Type: text/html; charset=utf-8');
echo Vista::renderizar($respuesta['datos']);
