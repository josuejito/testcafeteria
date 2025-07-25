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
if (!$conn) {
    die("Error de conexión: " . print_r(sqlsrv_errors(), true));
}

// Obtener productos
$sql = "SELECT * FROM Productos";
$stmt = sqlsrv_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
    }
    header {
      background: #2c3e50;
      color: white;
      padding: 20px;
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
    }
    .container {
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    .btn {
      padding: 5px 10px;
      text-decoration: none;
      color: white;
      border-radius: 5px;
    }
    .btn-green { background: #27ae60; }
    .btn-red { background: #e74c3c; }
  </style>
</head>
<body>
  <header><h1>Inventario de Productos</h1></header>

  <nav>
    <a href="inventario.php">Inicio</a>
    <a href="agregar.php">Agregar Producto</a>
    <a href="cerrar_sesion.php">Cerrar Sesión</a>
    <a href="ventas.php" class="btn btn-green">Registrar Venta</a>
    <a href="registro_ventas.php" class="btn">Ver Facturas</a>
  </nav>

  <div class="container">
    <h2>Lista de Productos</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= $row['nombre'] ?></td>
          <td><?= $row['descripcion'] ?></td>
          <td><?= $row['cantidad'] ?></td>
          <td>L. <?= number_format($row['precio'], 2) ?></td>
          <td>
            <a class="btn btn-green" href="editar.php?id=<?= $row['id'] ?>">Editar</a>
            <a class="btn btn-red" href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>

