<?php
$serverName = "tcp:cafeteriahn.database.windows.net,1433";
$connectionOptions = [
    "Database" => "cafeteria",
    "Uid" => "josuejorge@cafeteriahn",
    "PWD" => "Barcelona25"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) die(print_r(sqlsrv_errors(), true));

$sql = "SELECT V.id, P.nombre AS producto, V.cantidad, V.total, V.fecha
        FROM Ventas V
        INNER JOIN Productos P ON V.producto_id = P.id
        ORDER BY V.fecha DESC";
$stmt = sqlsrv_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Ventas</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Historial de Ventas</h2>
    <table>
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($v = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
            <tr>
                <td><?= $v['id'] ?></td>
                <td><?= $v['producto'] ?></td>
                <td><?= $v['cantidad'] ?></td>
                <td>L. <?= number_format($v['total'], 2) ?></td>
                <td><?= $v['fecha']->format('Y-m-d H:i:s') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
