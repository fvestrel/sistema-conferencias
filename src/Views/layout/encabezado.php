<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de conferencias</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header><h1>Sistema de conferencias</h1>
<p>Registro de conferencias y asistentes</p></header>
<nav aria-label="Secciones">
<?php foreach ($secciones as $nombre => $titulo): ?>
  <a class="<?= $nombre === $seccion ? 'activo' : '' ?>" href="?seccion=<?= e($nombre) ?>"><?= e($titulo) ?></a>
<?php endforeach ?>
</nav>
<main>
