<?php
function load_env_file(string $filePath): void {
  if (!is_file($filePath)) {
    return;
  }

  $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!$lines) {
    return;
  }

  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0) {
      continue;
    }
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) {
      continue;
    }
    $key = trim($parts[0]);
    $value = trim($parts[1]);
    $value = trim($value, "\"'");

    if ($key !== '') {
      putenv("$key=$value");
      $_ENV[$key] = $value;
    }
  }
}

function env_value(string $key, $default = null) {
  $val = getenv($key);
  if ($val !== false && $val !== '') {
    return $val;
  }
  if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
    return $_ENV[$key];
  }
  return $default;
}

$projectRoot = dirname(__DIR__);
$httpHost = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
$isLocalHost = $httpHost === ''
  || strpos($httpHost, 'localhost') !== false
  || strpos($httpHost, '.test') !== false
  || strpos($httpHost, '127.0.0.1') === 0
  || strpos($httpHost, '::1') === 0;

$appEnv = (string)(env_value('APP_ENV', $isLocalHost ? 'local' : 'production'));
load_env_file($projectRoot . '/.env');
load_env_file($projectRoot . ($appEnv === 'production' ? '/.env.production' : '/.env.local'));

$host = (string)env_value('INV_DB_HOST', 'localhost');
$port = (int)env_value('INV_DB_PORT', 3306);
$user = (string)env_value('INV_DB_USER', 'root');
$pass = (string)env_value('INV_DB_PASS', '');
$db = (string)env_value('INV_DB_NAME', 'invitacionweb');

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
  http_response_code(500);
  die('Error de conexion.');
}

$conn->set_charset('utf8mb4');

$conn->query("
  CREATE TABLE IF NOT EXISTS invitacion_unidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('persona', 'familia') NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    codigo_confirmacion VARCHAR(32) NOT NULL UNIQUE,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  )
");

$conn->query("
  CREATE TABLE IF NOT EXISTS invitacion_persona (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unidad_id INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    asistencia TINYINT(1) NOT NULL DEFAULT 0,
    asistio TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_persona_unidad FOREIGN KEY (unidad_id) REFERENCES invitacion_unidad(id) ON DELETE CASCADE
  )
");

$conn->query("
  CREATE TABLE IF NOT EXISTS confirmacion_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unidad_id INT NOT NULL,
    persona_id INT NOT NULL,
    accion ENUM('confirmo', 'cancelo', 'asistio') NOT NULL,
    fecha_evento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_hist_unidad FOREIGN KEY (unidad_id) REFERENCES invitacion_unidad(id) ON DELETE CASCADE,
    CONSTRAINT fk_hist_persona FOREIGN KEY (persona_id) REFERENCES invitacion_persona(id) ON DELETE CASCADE
  )
");

$colRes = $conn->query("SHOW COLUMNS FROM invitacion_persona LIKE 'asistio'");
if ($colRes && $colRes->num_rows === 0) {
  $conn->query("ALTER TABLE invitacion_persona ADD COLUMN asistio TINYINT(1) NOT NULL DEFAULT 0 AFTER asistencia");
}

$idxRes = $conn->query("SHOW INDEX FROM invitacion_persona WHERE Key_name = 'idx_asistio'");
if ($idxRes && $idxRes->num_rows === 0) {
  $conn->query("CREATE INDEX idx_asistio ON invitacion_persona (asistio)");
}

$idxRes2 = $conn->query("SHOW INDEX FROM invitacion_persona WHERE Key_name = 'idx_unidad_asistio'");
if ($idxRes2 && $idxRes2->num_rows === 0) {
  $conn->query("CREATE INDEX idx_unidad_asistio ON invitacion_persona (unidad_id, asistio)");
}

$accionRes = $conn->query("SHOW COLUMNS FROM confirmacion_historial LIKE 'accion'");
if ($accionRes && ($row = $accionRes->fetch_assoc())) {
  if (strpos((string)$row['Type'], 'asistio') === false) {
    $conn->query("ALTER TABLE confirmacion_historial MODIFY accion ENUM('confirmo','cancelo','asistio') NOT NULL");
  }
}
