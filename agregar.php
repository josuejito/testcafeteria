<?php
// Procesar si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    $serverName = "tcp:cafeteriahn.database.windows.net,1433";
    $connectionOptions = [
        "Database" => "cafeteria",
        "Uid" => "josuejorge@cafeteriahn",
        "PWD" => "Barcelona25"
    ];

    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if (!$conn) {
        die(print_r(sqlsrv_errors(), true));
    }

    $sql = "INSERT INTO Productos (nombre, descripcion, cantidad, precio) VALUES (?, ?, ?, ?)";
    $params = [$nombre, $descripcion, $cantidad, $precio];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        header("Location: inventario.php");
        exit();
    } else {
        echo "Error al insertar el producto.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        form { background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: auto; }
        label { display: block; margin: 10px 0 5px; }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 8px; margin-bottom: 15px;
        }
        input[type="submit"] {
            background: #27ae60; color: white; padding: 10px 20px;
            border: none; border-radius: 5px; cursor: pointer;
        }
        a { text-decoration: none; color: #2980b9; display: block; margin-top: 15px; }
    </style>
</head>
<body>
    <header><h1>Inventario de Productos</h1></header>

  <nav>
    <a href="inventario.php">Inicio</a>
    <a href="agregar.php">Agregar Producto</a>
    <a href="index.php">Cerrar Sesión</a>
    <a href="ventas.php" class="btn btn-green">Registrar Venta</a>
    <a href="registro_ventas.php" class="btn">Ver Facturas</a>
  
  </nav>
    <h2>Agregar Nuevo Producto</h2>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <label>Descripción:</label>
        <input type="text" name="descripcion" required>
        <label>Cantidad:</label>
        <input type="number" name="cantidad" required>
        <label>Precio:</label>
        <input type="number" name="precio" step="0.01" required>
        <input type="submit" value="Guardar">
        <a href="inventario.php">← Volver</a>
    </form>
</body>
</html>
