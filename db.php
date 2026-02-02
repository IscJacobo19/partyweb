<?php
// Conexión a la base de datos MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'invitacionweb';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
?>