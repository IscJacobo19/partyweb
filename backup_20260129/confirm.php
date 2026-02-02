<?php
// Copia de seguridad
require '../db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = $conn->real_escape_string(trim($_POST['codigo']));
    $sql = "SELECT * FROM invitados WHERE codigo = '$codigo'";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $conn->query("UPDATE invitados SET asistencia = 1 WHERE codigo = '$codigo'");
        $msg = '¡Gracias por confirmar tu asistencia!';
    } else {
        $msg = 'Código no válido. Intenta de nuevo.';
    }
}
$conn->close();
header('Content-Type: text/html; charset=utf-8');
echo $msg;
