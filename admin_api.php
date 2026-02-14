<?php
require __DIR__ . '/admin_auth.php';
admin_require_login();
require __DIR__ . '/api/db.php';

header('Content-Type: application/json; charset=utf-8');

function json_response(bool $ok, array $payload = [], int $http = 200): void {
  http_response_code($http);
  echo json_encode(array_merge(['ok' => $ok], $payload), JSON_UNESCAPED_UNICODE);
  exit;
}

function normalize_scanned_code(string $raw): string {
  $raw = trim($raw);
  if ($raw === '') return '';
  if (stripos($raw, 'confirmacion:') === 0) {
    $parts = explode(':', $raw, 2);
    return strtoupper(trim((string)($parts[1] ?? '')));
  }
  if (preg_match('/(?:confirmacion[:=\s-]*)?([a-z0-9]{4,12})/i', $raw, $m)) {
    return strtoupper((string)$m[1]);
  }
  return strtoupper($raw);
}

function decode_qr_from_image_file(string $tmpPath): ?string {
  if (!function_exists('curl_init')) {
    return null;
  }
  $ch = curl_init('https://api.qrserver.com/v1/read-qr-code/');
  $post = ['file' => new CURLFile($tmpPath)];
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
  $resp = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($resp === false || $status >= 400) {
    return null;
  }
  $json = json_decode($resp, true);
  if (!is_array($json) || empty($json[0]['symbol'][0]['data'])) {
    return null;
  }
  return trim((string)$json[0]['symbol'][0]['data']);
}

function generate_codigo(mysqli $conn, int $length = 4): string {
  $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  $maxAttempts = 40;
  for ($i = 0; $i < $maxAttempts; $i++) {
    $code = '';
    for ($j = 0; $j < $length; $j++) {
      $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    $stmt = $conn->prepare("SELECT id FROM invitacion_unidad WHERE codigo_confirmacion = ? LIMIT 1");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$exists) {
      return $code;
    }
  }
  throw new RuntimeException('No se pudo generar un código único.');
}

function load_admin_state(mysqli $conn, string $kpiFilter = 'all'): array {
  $personasRes = $conn->query("
    SELECT p.unidad_id, p.id, p.nombre, p.asistencia, p.asistio
    FROM invitacion_persona p
    INNER JOIN invitacion_unidad u ON u.id = p.unidad_id
    WHERE u.activo = 1
    ORDER BY p.nombre ASC
  ");

  $personasByUnidad = [];
  $totalPersonas = 0;
  $totalConfirmadas = 0;
  $totalAsistidos = 0;
  while ($personasRes && ($p = $personasRes->fetch_assoc())) {
    $uid = (int)$p['unidad_id'];
    if (!isset($personasByUnidad[$uid])) {
      $personasByUnidad[$uid] = [];
    }
    $personasByUnidad[$uid][] = [
      'id' => (int)$p['id'],
      'nombre' => $p['nombre'],
      'asistencia' => (int)$p['asistencia'],
      'asistio' => (int)$p['asistio'],
    ];
    $totalPersonas++;
    if ((int)$p['asistencia'] === 1) {
      $totalConfirmadas++;
    }
    if ((int)$p['asistio'] === 1) {
      $totalAsistidos++;
    }
  }

  $totalPendientes = $totalPersonas - $totalConfirmadas;

  $having = '';
  if ($kpiFilter === 'confirmados') {
    $having = 'HAVING COALESCE(SUM(CASE WHEN p.asistencia = 1 THEN 1 ELSE 0 END), 0) > 0';
  } elseif ($kpiFilter === 'pendientes') {
    $having = 'HAVING COALESCE(SUM(CASE WHEN p.asistencia = 1 THEN 1 ELSE 0 END), 0) = 0';
  } elseif ($kpiFilter === 'asistidos') {
    $having = 'HAVING COALESCE(SUM(CASE WHEN p.asistio = 1 THEN 1 ELSE 0 END), 0) > 0';
  }

  $unidadesRes = $conn->query("
    SELECT
      u.id,
      u.tipo,
      u.nombre,
      u.codigo_confirmacion,
      COUNT(p.id) AS personas_total,
      SUM(CASE WHEN p.asistencia = 1 THEN 1 ELSE 0 END) AS personas_confirmadas,
      SUM(CASE WHEN p.asistio = 1 THEN 1 ELSE 0 END) AS personas_asistidas
    FROM invitacion_unidad u
    LEFT JOIN invitacion_persona p ON p.unidad_id = u.id
    WHERE u.activo = 1
    GROUP BY u.id, u.tipo, u.nombre, u.codigo_confirmacion
    $having
    ORDER BY u.created_at DESC
  ");

  $unidades = [];
  while ($unidadesRes && ($u = $unidadesRes->fetch_assoc())) {
    $uid = (int)$u['id'];
    $members = $personasByUnidad[$uid] ?? [];

    $searchChunks = [
      $u['tipo'],
      $u['nombre'],
      $u['codigo_confirmacion'],
      ((int)$u['personas_confirmadas'] > 0 ? 'confirmado' : 'pendiente'),
    ];
    foreach ($members as $m) {
      $searchChunks[] = $m['nombre'];
      $searchChunks[] = ((int)$m['asistencia'] === 1 ? 'confirmado' : 'pendiente');
      if ((int)$m['asistio'] === 1) {
        $searchChunks[] = 'asistio';
      }
    }

    $unidades[] = [
      'id' => $uid,
      'tipo' => $u['tipo'],
      'nombre' => $u['nombre'],
      'codigo_confirmacion' => $u['codigo_confirmacion'],
      'personas_total' => (int)$u['personas_total'],
      'personas_confirmadas' => (int)$u['personas_confirmadas'],
      'personas_asistidas' => (int)$u['personas_asistidas'],
      'members' => $members,
      'search' => mb_strtolower(implode(' ', $searchChunks), 'UTF-8'),
    ];
  }

  $histRes = $conn->query("
    SELECT
      h.fecha_evento,
      h.accion,
      u.nombre AS unidad_nombre,
      p.nombre AS persona_nombre
    FROM confirmacion_historial h
    INNER JOIN invitacion_unidad u ON u.id = h.unidad_id
    INNER JOIN invitacion_persona p ON p.id = h.persona_id
    ORDER BY h.fecha_evento DESC
    LIMIT 200
  ");

  $historial = [];
  while ($histRes && ($h = $histRes->fetch_assoc())) {
    $historial[] = $h;
  }

  return [
    'kpis' => [
      'total_personas' => $totalPersonas,
      'total_confirmadas' => $totalConfirmadas,
      'total_pendientes' => $totalPendientes,
      'total_asistidos' => $totalAsistidos,
    ],
    'unidades' => $unidades,
    'historial' => $historial,
  ];
}

$action = $_GET['action'] ?? '';
if ($action === 'state') {
  $kpiFilter = trim((string)($_GET['kpi_filter'] ?? 'all'));
  if (!in_array($kpiFilter, ['all', 'confirmados', 'pendientes', 'asistidos'], true)) {
    $kpiFilter = 'all';
  }
  json_response(true, load_admin_state($conn, $kpiFilter));
}

if ($action === 'lookup_code') {
  $codigo = trim((string)($_GET['codigo'] ?? ''));
  if ($codigo === '') {
    json_response(false, ['message' => 'Código inválido.'], 422);
  }
  $stmt = $conn->prepare("
    SELECT id, nombre
    FROM invitacion_unidad
    WHERE codigo_confirmacion = ? AND activo = 1
    LIMIT 1
  ");
  $stmt->bind_param('s', $codigo);
  $stmt->execute();
  $unidad = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  if (!$unidad) {
    json_response(false, ['message' => 'Código no encontrado.'], 404);
  }
  $uid = (int)$unidad['id'];
  $personasRes = $conn->query("
    SELECT id, nombre, asistio, asistencia
    FROM invitacion_persona
    WHERE unidad_id = $uid
    ORDER BY nombre ASC
  ");
  $personas = [];
  while ($personasRes && ($p = $personasRes->fetch_assoc())) {
    $personas[] = [
      'id' => (int)$p['id'],
      'nombre' => $p['nombre'],
      'asistio' => (int)$p['asistio'],
      'asistencia' => (int)$p['asistencia'],
    ];
  }
  json_response(true, [
    'unidad' => [
      'id' => $uid,
      'nombre' => $unidad['nombre'],
    ],
    'personas' => $personas,
  ]);
}

if ($action === 'decode_qr_image' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_FILES['qr_image']) || !is_array($_FILES['qr_image'])) {
    json_response(false, ['message' => 'Imagen no enviada.'], 422);
  }
  $file = $_FILES['qr_image'];
  $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
  if ($err !== UPLOAD_ERR_OK) {
    json_response(false, ['message' => 'No se pudo cargar la imagen.'], 422);
  }
  $tmp = (string)($file['tmp_name'] ?? '');
  if ($tmp === '' || !is_uploaded_file($tmp)) {
    json_response(false, ['message' => 'Archivo inválido.'], 422);
  }
  $size = (int)($file['size'] ?? 0);
  if ($size <= 0 || $size > 8 * 1024 * 1024) {
    json_response(false, ['message' => 'Imagen demasiado grande.'], 422);
  }
  $mime = '';
  if (function_exists('finfo_open')) {
    $fi = finfo_open(FILEINFO_MIME_TYPE);
    if ($fi) {
      $mime = (string)finfo_file($fi, $tmp);
      finfo_close($fi);
    }
  }
  if ($mime !== '' && stripos($mime, 'image/') !== 0) {
    json_response(false, ['message' => 'Solo se permiten imágenes.'], 422);
  }

  $raw = decode_qr_from_image_file($tmp);
  if ($raw === null || $raw === '') {
    json_response(false, ['message' => 'No se detectó un QR en la imagen.'], 404);
  }
  $code = normalize_scanned_code($raw);
  if ($code === '') {
    json_response(false, ['message' => 'No se detectó un código válido.'], 404);
  }
  json_response(true, ['raw' => $raw, 'code' => $code]);
}

if ($action === 'checkin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $idsRaw = trim((string)($_POST['persona_ids'] ?? ''));
  $personaIds = [];
  if ($idsRaw !== '') {
    foreach (explode(',', $idsRaw) as $val) {
      $id = (int)trim($val);
      if ($id > 0) {
        $personaIds[] = $id;
      }
    }
  }
  if (empty($personaIds)) {
    json_response(false, ['message' => 'Personas inválidas.'], 422);
  }

  $in = implode(',', array_map('intval', $personaIds));
  $validRes = $conn->query("SELECT id, asistencia FROM invitacion_persona WHERE id IN ($in)");
  $validIds = [];
  while ($validRes && ($row = $validRes->fetch_assoc())) {
    if ((int)$row['asistencia'] === 1) {
      $validIds[] = (int)$row['id'];
    }
  }
  if (empty($validIds)) {
    json_response(false, ['message' => 'Solo puedes registrar asistencia de personas confirmadas.'], 422);
  }
  $validIn = implode(',', array_map('intval', $validIds));
  $conn->query("UPDATE invitacion_persona SET asistio = 1 WHERE id IN ($validIn)");
  $conn->query("
    INSERT INTO confirmacion_historial (unidad_id, persona_id, accion)
    SELECT unidad_id, id, 'asistio'
    FROM invitacion_persona
    WHERE id IN ($validIn)
  ");

  json_response(true, array_merge(['message' => 'Asistencia registrada.'], load_admin_state($conn)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tipo = trim((string)($_POST['tipo'] ?? ''));
  $nombreUnidad = trim((string)($_POST['nombre_unidad'] ?? ''));
  $codigo = trim((string)($_POST['codigo_confirmacion'] ?? ''));
  $miembrosRaw = trim((string)($_POST['miembros'] ?? ''));

  if (!in_array($tipo, ['persona', 'familia'], true)) {
    json_response(false, ['message' => 'Selecciona un tipo válido.'], 422);
  }
  if ($nombreUnidad === '') {
    json_response(false, ['message' => 'Nombre es obligatorio.'], 422);
  }

  $miembros = [];
  if ($tipo === 'persona') {
    $miembros[] = $nombreUnidad;
  } else {
    $lines = preg_split('/\r\n|\r|\n/', $miembrosRaw);
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line !== '') {
        $miembros[] = $line;
      }
    }
  }

  if (empty($miembros)) {
    json_response(false, ['message' => 'Debes agregar al menos una persona.'], 422);
  }

  try {
    $codigo = generate_codigo($conn, 4);
  } catch (Throwable $e) {
    json_response(false, ['message' => 'No se pudo generar un código único.'], 500);
  }

  $conn->begin_transaction();
  try {
    $stmtUnidad = $conn->prepare("
      INSERT INTO invitacion_unidad (tipo, nombre, codigo_confirmacion, activo)
      VALUES (?, ?, ?, 1)
    ");
    $stmtUnidad->bind_param('sss', $tipo, $nombreUnidad, $codigo);
    $stmtUnidad->execute();
    $unidadId = (int)$stmtUnidad->insert_id;
    $stmtUnidad->close();

    $stmtPersona = $conn->prepare("
      INSERT INTO invitacion_persona (unidad_id, nombre, asistencia)
      VALUES (?, ?, 0)
    ");
    foreach ($miembros as $miembro) {
      $stmtPersona->bind_param('is', $unidadId, $miembro);
      $stmtPersona->execute();
    }
    $stmtPersona->close();

    $conn->commit();
  } catch (Throwable $e) {
    $conn->rollback();
    json_response(false, ['message' => 'No se pudo guardar. Verifica los datos.'], 500);
  }

  json_response(
    true,
    array_merge(['message' => 'Invitación guardada correctamente.'], load_admin_state($conn))
  );
}

json_response(false, ['message' => 'Acción no válida.'], 404);
