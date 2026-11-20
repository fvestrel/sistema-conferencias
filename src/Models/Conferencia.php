<?php
declare(strict_types=1);

final class Conferencia extends Registro
{
    protected string $tabla = 'Conferencias';
    protected string $clave = 'conferencia_id';
    protected array $campos = [
        'conferencista_id' => ['label' => 'Conferencista', 'max' => 10, 'type' => 'select', 'source' => 'conferencistas'],
        'nombre' => ['label' => 'Nombre', 'max' => 150],
        'fecha' => ['label' => 'Fecha', 'max' => 10, 'type' => 'date'],
        'ubicacion' => ['label' => 'Ubicación', 'max' => 200],
        'descripcion' => ['label' => 'Descripción', 'max' => 5000, 'type' => 'textarea'],
    ];
}
