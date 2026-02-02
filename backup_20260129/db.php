<?php
// Copia de seguridad
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'invitacionweb';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
