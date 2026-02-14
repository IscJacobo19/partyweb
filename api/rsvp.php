<?php
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $message, $http = 200) {
  http_response_code($http);
  echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  respond(false, 'Método no permitido.', 405);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload)) {
  $payload = $_POST;
}

$unidadId = isset($payload['unidad_id']) ? (int)$payload['unidad_id'] : 0;
$codigo = isset($payload['codigo']) ? trim((string)$payload['codigo']) : '';
$miembros = isset($payload['miembros']) && is_array($payload['miembros']) ? $payload['miembros'] : [];

if ($unidadId <= 0 || $codigo === '') {
  respond(false, 'Datos incompletos.', 422);
}

$stmt = $conn->prepare('SELECT id, codigo_confirmacion FROM invitacion_unidad WHERE id = ? AND activo = 1 LIMIT 1');
if (!$stmt) {
  respond(false, 'Error de servidor.', 500);
}

$stmt->bind_param('i', $unidadId);
$stmt->execute();
$unidad = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$unidad) {
  respond(false, 'Invitación no encontrada.', 404);
}

if (!hash_equals((string)$unidad['codigo_confirmacion'], $codigo)) {
  respond(false, 'Código de confirmación inválido.', 401);
}

$conn->begin_transaction();
try {
  $ids = array_map('intval', $miembros);
  $ids = array_values(array_filter($ids, fn($v) => $v > 0));

  $personasRes = $conn->query('SELECT id, asistencia FROM invitacion_persona WHERE unidad_id = ' . (int)$unidadId);
  $personasActuales = [];
  while ($personasRes && ($p = $personasRes->fetch_assoc())) {
    $personasActuales[(int)$p['id']] = (int)$p['asistencia'];
  }

  $conn->query('UPDATE invitacion_persona SET asistencia = 0 WHERE unidad_id = ' . (int)$unidadId);
  if (!empty($ids)) {
    $in = implode(',', $ids);
    $conn->query('UPDATE invitacion_persona SET asistencia = 1 WHERE unidad_id = ' . (int)$unidadId . " AND id IN ($in)");
  }

  $hist = $conn->prepare('INSERT INTO confirmacion_historial (unidad_id, persona_id, accion) VALUES (?, ?, ?)');
  foreach ($personasActuales as $personaId => $estadoAnterior) {
    $estadoNuevo = in_array($personaId, $ids, true) ? 1 : 0;
    if ($estadoAnterior !== $estadoNuevo) {
      $accion = $estadoNuevo === 1 ? 'confirmo' : 'cancelo';
      $hist->bind_param('iis', $unidadId, $personaId, $accion);
      $hist->execute();
    }
  }
  $hist->close();

  $sumRes = $conn->query('SELECT COUNT(*) AS total_confirmados FROM invitacion_persona WHERE unidad_id = ' . (int)$unidadId . ' AND asistencia = 1');
  $sum = $sumRes ? (int)$sumRes->fetch_assoc()['total_confirmados'] : 0;

  $conn->commit();
  if ($sum > 0) {
    respond(true, 'Confirmación registrada correctamente.');
  }
  respond(true, 'Asistencia cancelada correctamente.');
} catch (Throwable $e) {
  $conn->rollback();
  respond(false, 'No se pudo guardar la confirmación.', 500);
}
