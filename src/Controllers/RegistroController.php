<?php
declare(strict_types=1);


final class RegistroController
{
    private ?PDO $db;

    public function __construct(?PDO $db) { $this->db = $db; }

    public function procesar(array $get, array $post, string $metodo, array &$sesion): array
    {
        $secciones = ['conferencistas' => 'Conferencistas', 'conferencias' => 'Conferencias',
            'asistentes' => 'Asistentes', 'inscripciones' => 'Inscripciones'];
        $seccion = $get['seccion'] ?? 'conferencias';
        if (!is_string($seccion) || !isset($secciones[$seccion])) {
            return ['estado' => 404, 'texto' => 'Sección no encontrada.'];
        }
        if (!in_array($metodo, ['GET', 'POST'], true)) {
            return ['estado' => 405, 'texto' => 'Método no permitido.'];
        }
        $sesion['csrf'] ??= bin2hex(random_bytes(32));
        $datos = [
            'secciones' => $secciones, 'seccion' => $seccion, 'csrf' => $sesion['csrf'],
            'mensaje' => $sesion['mensaje'] ?? '', 'error' => '', 'fatal' => false,
            'edicion' => null, 'registros' => [], 'opciones' => [], 'campos' => [], 'clave' => '',
        ];
        unset($sesion['mensaje']);
        try {
            if ($this->db === null) {
                throw new PDOException('Conexión no disponible.');
            }
            $modelos = ['conferencistas' => new Conferencista($this->db),
                'conferencias' => new Conferencia($this->db),
                'asistentes' => new Asistente($this->db), 'inscripciones' => new Inscripcion($this->db)];
            $modelo = $modelos[$seccion];
            $datos['campos'] = $modelo->campos();
            $datos['clave'] = $modelo->clave();

            if ($metodo === 'POST') {
                try {
                    if (!is_string($post['csrf'] ?? null) || !hash_equals($sesion['csrf'], $post['csrf'])) {
                        throw new InvalidArgumentException('El formulario caducó. Actualiza la página e inténtalo otra vez.');
                    }
                    $accion = $post['accion'] ?? '';
                    $id = $this->idValido($post['id'] ?? '');
                    if (($post['id'] ?? '') !== '' && $id === null) {
                        throw new InvalidArgumentException('Identificador no válido.');
                    }
                    if ($accion === 'eliminar' && $id !== null) {
                        $modelo->eliminar($id);
                        $sesion['mensaje'] = 'Registro eliminado correctamente.';
                    } elseif ($accion === 'guardar') {
                        $modelo->guardar($post, $id);
                        $sesion['mensaje'] = $id ? 'Registro actualizado correctamente.' : 'Registro creado correctamente.';
                    } else {
                        throw new InvalidArgumentException('Acción no válida.');
                    }
                    return ['estado' => 303, 'redireccion' => '?seccion=' . $seccion];
                } catch (InvalidArgumentException $ex) {
                    $datos['error'] = $ex->getMessage();
                } catch (PDOException $ex) {
                    error_log((string)$ex);
                    $datos['error'] = $this->mensajeBaseDatos($ex);
                }
                if (($post['accion'] ?? '') === 'guardar') {
                    $datos['edicion'] = $post;
                    $datos['edicion'][$modelo->clave()] = $this->idValido($post['id'] ?? '') ?? '';
                }
            } elseif (isset($get['editar'])) {
                $id = $this->idValido($get['editar']);
                $datos['edicion'] = $id ? $modelo->buscar($id) : null;
                if (!$datos['edicion']) { $datos['error'] = 'No se encontró el registro solicitado.'; }
            }

            $datos['registros'] = $modelo->listar();
            // La vista recibe datos y etiquetas, nunca objetos de acceso a la base.
            foreach ($modelos as $nombre => $objeto) {
                $datos['opciones'][$nombre] = [];
                foreach ($objeto->listar() as $fila) {
                    $texto = $fila['nombre'] ?? ('Registro ' . $fila[$objeto->clave()]);
                    if (isset($fila['apellido'])) { $texto .= ' ' . $fila['apellido']; }
                    $datos['opciones'][$nombre][$fila[$objeto->clave()]] = $texto;
                }
            }
        } catch (PDOException $ex) {
            error_log((string)$ex);
            $datos['fatal'] = true;
            $datos['error'] = 'No se pudo abrir la base de datos. Inicia MySQL, importa database/esquema.sql y revisa config/database.php.';
            return ['estado' => 503, 'datos' => $datos];
        }
        return ['estado' => 200, 'datos' => $datos];
    }

    private function idValido($valor): ?int
    {
        if (!is_string($valor) || !ctype_digit($valor) || (int)$valor < 1 || (int)$valor > 4294967295) {
            return null;
        }
        return (int)$valor;
    }

    private function mensajeBaseDatos(PDOException $ex): string
    {
        return match ((int)($ex->errorInfo[1] ?? 0)) {
            1062 => 'Este asistente ya está inscrito en esa conferencia.',
            1451 => 'No se puede eliminar: el registro está relacionado con una conferencia o inscripción. Elimina o modifica primero esa relación.',
            1452 => 'La conferencia, el asistente o el conferencista seleccionado ya no existe. Actualiza la página.',
            default => 'No se pudo guardar el cambio. Revisa la conexión e inténtalo nuevamente.',
        };
    }
}
