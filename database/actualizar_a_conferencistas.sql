-- SOLO para quien ya importó la versión anterior con la tabla Ponentes.
-- Realiza una copia de seguridad desde phpMyAdmin antes de ejecutar.
-- No ejecutar en una instalación nueva ni repetir esta actualización.
USE gestion_conferencias;

ALTER TABLE Conferencias DROP FOREIGN KEY fk_conferencia_ponente;
RENAME TABLE Ponentes TO Conferencistas;
ALTER TABLE Conferencistas
  CHANGE ponente_id conferencista_id INT UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE Conferencias
  CHANGE ponente_id conferencista_id INT UNSIGNED NOT NULL,
  ADD CONSTRAINT fk_conferencia_conferencista FOREIGN KEY (conferencista_id)
    REFERENCES Conferencistas(conferencista_id)
    ON DELETE RESTRICT ON UPDATE RESTRICT;
