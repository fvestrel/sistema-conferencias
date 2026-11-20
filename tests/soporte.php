<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/bootstrap.php';

function basePrueba(): PDO
{
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA foreign_keys = ON');
    $db->exec('CREATE TABLE Conferencistas (conferencista_id INTEGER PRIMARY KEY, nombre TEXT, apellido TEXT, especialidad TEXT, email TEXT)');
    $db->exec('CREATE TABLE Conferencias (conferencia_id INTEGER PRIMARY KEY, nombre TEXT, fecha TEXT, ubicacion TEXT, descripcion TEXT, conferencista_id INTEGER REFERENCES Conferencistas(conferencista_id) ON DELETE RESTRICT)');
    $db->exec('CREATE TABLE Asistentes (asistente_id INTEGER PRIMARY KEY, nombre TEXT, apellido TEXT, email TEXT, telefono TEXT)');
    $db->exec('CREATE TABLE Inscripciones (inscripcion_id INTEGER PRIMARY KEY, conferencia_id INTEGER REFERENCES Conferencias(conferencia_id) ON DELETE RESTRICT, asistente_id INTEGER REFERENCES Asistentes(asistente_id) ON DELETE RESTRICT, fecha_inscripcion TEXT DEFAULT CURRENT_TIMESTAMP, UNIQUE(conferencia_id, asistente_id))');
    return $db;
}
