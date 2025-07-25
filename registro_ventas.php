<?php
session_start();

// Evitar cache para que no se pueda usar flecha atrás para ver páginas protegidas
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Verificar sesión antes de mostrar contenido
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$serverName = "tcp:cafeteriahn.database.windows.net,1433";
$connectionOptions = [
    "Database" => "cafeteria",
    "Uid" => "josuejorge@cafeteriahn",
    "PWD" => "Barcelona25"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) die(print_r(sqlsrv_errors(), true));

// Obtener todas las facturas con sus ventas
$sql = "
    SELECT 
        F.id AS factura_id,
        F.fecha,
        P.nombre AS producto,
        V.cantidad,
        V.total
    FROM Facturas F
    INNER JOIN Ventas V ON V.factura_id = F.id
    INNER JOIN Productos P ON P.id = V.producto_id
    ORDER BY F.id DESC, V.id ASC
";
$stmt = sqlsrv_query($conn, $sql);

// Agrupar ventas por factura
$facturas = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $fid = $row['factura_id'];
    if (!isset($facturas[$fid])) {
        $facturas[$fid] = [
            'fecha' => $row['fecha'],
            'items' => []
        ];
    }
    $facturas[$fid]['items'][] = [
        'producto' => $row['producto'],
        'cantidad' => $row['cantidad'],
        'total' => $row['total']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        header {
      background: #2c3e50;
      color: white;
      padding: 10px;
      text-align: center;
    }
         nav {
  background: #34495e;
  padding: 10px;
  text-align: center;
}

nav a {
  color: white;
  margin: 0 15px;
  text-decoration: none;
  font-weight: bold;
  display: inline-block; /* Esto hace que los links estén en línea */
}
        .factura {
            background: white; border: 1px solid #ccc; border-radius: 10px;
            padding: 20px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        h2 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background: #3498db; color: white; }
        .total { font-weight: bold; text-align: right; padding-top: 10px; }
    </style>
</head>
<body>
<header><h1>Facturas</h1></header>

  <nav>
    <a href="inventario.php">Inicio</a>
    <a href="agregar.php">Agregar Producto</a>
    <a href="index.php">Cerrar Sesión</a>
    <a href="ventas.php" class="btn btn-green">Registrar Venta</a>
    <a href="registro_ventas.php" class="btn">Ver Facturas</a>
  
  </nav>
    
    <h2>Historial de Ventas</h2>

    <?php foreach ($facturas as $id => $factura): ?>
        <div class="factura">
            <h2>Factura #<?= $id ?></h2>
            <p><strong>Fecha:</strong> <?= $factura['fecha']->format('Y-m-d H:i:s') ?></p>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Total (Lps)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $suma = 0;
                    foreach ($factura['items'] as $item):
                        $suma += $item['total'];
                    ?>
                        <tr>
                            <td><?= $item['producto'] ?></td>
                            <td><?= $item['cantidad'] ?></td>
                            <td><?= number_format($item['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">Total Factura: L. <?= number_format($suma, 2) ?></div>
        </div>
    <?php endforeach; ?>
</body>
</html>

