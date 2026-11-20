-- Importar en phpMyAdmin. No elimina tablas ni registros existentes.
CREATE DATABASE IF NOT EXISTS gestion_conferencias
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_conferencias;

CREATE TABLE IF NOT EXISTS Conferencistas (
  conferencista_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  especialidad VARCHAR(150) NOT NULL,
  email VARCHAR(254) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Conferencias (
  conferencia_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  fecha DATE NOT NULL,
  ubicacion VARCHAR(200) NOT NULL,
  descripcion TEXT NOT NULL,
  conferencista_id INT UNSIGNED NOT NULL,
  CONSTRAINT fk_conferencia_conferencista FOREIGN KEY (conferencista_id)
    REFERENCES Conferencistas(conferencista_id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Asistentes (
  asistente_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  email VARCHAR(254) NOT NULL,
  telefono VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Inscripciones (
  inscripcion_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  conferencia_id INT UNSIGNED NOT NULL,
  asistente_id INT UNSIGNED NOT NULL,
  fecha_inscripcion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_inscripcion UNIQUE (conferencia_id, asistente_id),
  CONSTRAINT fk_inscripcion_conferencia FOREIGN KEY (conferencia_id)
    REFERENCES Conferencias(conferencia_id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_inscripcion_asistente FOREIGN KEY (asistente_id)
    REFERENCES Asistentes(asistente_id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB;
