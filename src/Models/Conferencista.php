<?php
declare(strict_types=1);

final class Conferencista extends Registro
{
    protected string $tabla = 'Conferencistas';
    protected string $clave = 'conferencista_id';
    protected array $campos = [
        'nombre' => ['label' => 'Nombre', 'max' => 100],
        'apellido' => ['label' => 'Apellido', 'max' => 100],
        'especialidad' => ['label' => 'Especialidad', 'max' => 150],
        'email' => ['label' => 'Correo electrónico', 'max' => 254, 'type' => 'email'],
    ];
}
