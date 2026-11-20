<?php
declare(strict_types=1);

final class Asistente extends Registro
{
    protected string $tabla = 'Asistentes';
    protected string $clave = 'asistente_id';
    protected array $campos = [
        'nombre' => ['label' => 'Nombre', 'max' => 100],
        'apellido' => ['label' => 'Apellido', 'max' => 100],
        'email' => ['label' => 'Correo electrónico', 'max' => 254, 'type' => 'email'],
        'telefono' => ['label' => 'Teléfono', 'max' => 30, 'type' => 'tel'],
    ];
}
