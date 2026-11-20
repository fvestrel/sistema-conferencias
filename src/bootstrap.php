<?php
declare(strict_types=1);

require_once __DIR__ . '/Core/Database.php';
require_once __DIR__ . '/Core/Vista.php';
require_once __DIR__ . '/Models/Registro.php';
foreach (['Conferencia', 'Asistente', 'Conferencista', 'Inscripcion'] as $clase) {
    require_once __DIR__ . "/Models/$clase.php";
}
require_once __DIR__ . '/Controllers/RegistroController.php';

// Escapa cualquier dato antes de mostrarlo en el HTML.
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
