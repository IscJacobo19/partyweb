-- Copia de seguridad
-- Crea la base de datos y la tabla de invitados
CREATE DATABASE IF NOT EXISTS invitacionweb;
USE invitacionweb;

CREATE TABLE IF NOT EXISTS invitados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    asistencia TINYINT(1) DEFAULT 0
);

-- Ejemplo de invitados
INSERT INTO invitados (nombre, codigo) VALUES
('Juan Pérez', 'ABC123'),
('María López', 'DEF456'),
('Carlos Ruiz', 'GHI789');
