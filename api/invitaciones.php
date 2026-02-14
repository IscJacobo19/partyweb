<?php
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$sql = "
  SELECT
    u.id AS unidad_id,
    u.tipo,
    u.nombre AS unidad_nombre,
    p.id AS persona_id,
    p.nombre AS persona_nombre,
    p.asistencia
  FROM invitacion_unidad u
  LEFT JOIN invitacion_persona p ON p.unidad_id = u.id
  WHERE u.activo = 1
  ORDER BY u.nombre ASC, p.nombre ASC
";

$res = $conn->query($sql);
if (!$res) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'message' => 'No se pudo cargar la lista.']);
  exit;
}

$data = [];
while ($row = $res->fetch_assoc()) {
  $id = (int)$row['unidad_id'];
  if (!isset($data[$id])) {
    $data[$id] = [
      'id' => $id,
      'tipo' => $row['tipo'],
      'nombre' => $row['unidad_nombre'],
      'personas' => [],
    ];
  }

  if (!empty($row['persona_id'])) {
    $data[$id]['personas'][] = [
      'id' => (int)$row['persona_id'],
      'nombre' => $row['persona_nombre'],
      'asistencia' => (int)$row['asistencia'],
    ];
  }
}

echo json_encode([
  'ok' => true,
  'unidades' => array_values($data),
], JSON_UNESCAPED_UNICODE);
