<?php
declare(strict_types=1);

final class Vista
{
    public static function renderizar(array $datos): string
    {
        extract($datos, EXTR_SKIP);
        ob_start();
        try {
            require dirname(__DIR__) . '/Views/registros/index.php';
            return ob_get_clean();
        } catch (Throwable $ex) {
            ob_end_clean();
            throw $ex;
        }
    }
}
