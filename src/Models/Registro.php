<?php
declare(strict_types=1);

// Los nombres de tablas y campos provienen de las clases, nunca del formulario.
abstract class Registro
{
    protected PDO $db;
    protected string $tabla;
    protected string $clave;
    protected array $campos;

    public function __construct(PDO $db) { $this->db = $db; }

    public function listar(): array
    {
        return $this->db->query("SELECT * FROM {$this->tabla} ORDER BY {$this->clave} DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): ?array
    {
        $consulta = $this->db->prepare("SELECT * FROM {$this->tabla} WHERE {$this->clave} = ?");
        $consulta->execute([$id]);
        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function guardar(array $entrada, ?int $id = null): void
    {
        $datos = [];
        foreach ($this->campos as $campo => $regla) {
            $valor = $entrada[$campo] ?? '';
            if (!is_string($valor)) {
                throw new InvalidArgumentException('El formato de los datos no es válido.');
            }
            $valor = trim($valor);
            if ($valor === '' || mb_strlen($valor) > $regla['max']) {
                throw new InvalidArgumentException("Revisa el campo {$regla['label']}: es obligatorio y admite hasta {$regla['max']} caracteres.");
            }
            if (($regla['type'] ?? '') === 'email' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Escribe un correo electrónico válido.');
            }
            if (($regla['type'] ?? '') === 'date') {
                $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
                if (!$fecha || $fecha->format('Y-m-d') !== $valor) {
                    throw new InvalidArgumentException('Escribe una fecha válida.');
                }
            }
            $datos[$campo] = $valor;
            if (($regla['type'] ?? '') === 'select' && !preg_match('/^[1-9][0-9]*$/D', $valor)) {
                throw new InvalidArgumentException("Selecciona una opción válida para {$regla['label']}.");
            }
        }
        if ($id !== null && !$this->buscar($id)) {
            throw new InvalidArgumentException('El registro ya no existe.');
        }
        if ($id === null) {
            $columnas = implode(', ', array_keys($datos));
            $marcadores = implode(', ', array_fill(0, count($datos), '?'));
            $sql = "INSERT INTO {$this->tabla} ($columnas) VALUES ($marcadores)";
        } else {
            $asignaciones = implode(', ', array_map(fn($campo) => "$campo = ?", array_keys($datos)));
            $sql = "UPDATE {$this->tabla} SET $asignaciones WHERE {$this->clave} = ?";
            $datos[] = $id;
        }
        $this->db->prepare($sql)->execute(array_values($datos));
    }

    public function eliminar(int $id): void
    {
        $consulta = $this->db->prepare("DELETE FROM {$this->tabla} WHERE {$this->clave} = ?");
        $consulta->execute([$id]);
        if ($consulta->rowCount() === 0) {
            throw new InvalidArgumentException('El registro ya no existe.');
        }
    }

    public function campos(): array { return $this->campos; }
    public function clave(): string { return $this->clave; }
}
