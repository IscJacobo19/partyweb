<?php
require 'db.php';
$res = $conn->query("SELECT nombre, codigo, asistencia FROM invitados");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Invitados</title>
    <style>
        body { font-family: Arial; background: #fdf6f0; }
        table { border-collapse: collapse; margin: 30px auto; background: #fff; }
        th, td { border: 1px solid #b47a2b; padding: 8px 16px; }
        th { background: #b47a2b; color: #fff; }
        tr:nth-child(even) { background: #f7e7d3; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Lista de Invitados</h2>
    <table>
        <tr><th>Nombre</th><th>Código</th><th>Asistencia</th></tr>
        <?php while($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['codigo']) ?></td>
            <td><?= $row['asistencia'] ? 'Confirmado' : 'Pendiente' ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>