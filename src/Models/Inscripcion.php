<?php
declare(strict_types=1);

final class Inscripcion extends Registro
{
    protected string $tabla = 'Inscripciones';
    protected string $clave = 'inscripcion_id';
    protected array $campos = [
        'conferencia_id' => ['label' => 'Conferencia', 'max' => 10, 'type' => 'select', 'source' => 'conferencias'],
        'asistente_id' => ['label' => 'Asistente', 'max' => 10, 'type' => 'select', 'source' => 'asistentes'],
    ];
}
