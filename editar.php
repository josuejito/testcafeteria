<?php
$serverName = "tcp:cafeteriahn.database.windows.net,1433";
$connectionOptions = [
    "Database" => "cafeteria",
    "Uid" => "josuejorge@cafeteriahn",
    "PWD" => "Barcelona25"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) die(print_r(sqlsrv_errors(), true));

// Obtener producto
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM Productos WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);
    $producto = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}

// Procesar cambios
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];
    $sql = "UPDATE Productos SET nombre = ?, descripcion = ?, cantidad = ?, precio = ? WHERE id = ?";
    $params = [$nombre, $descripcion, $cantidad, $precio, $_GET['id']];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        header("Location: inventario.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        form { background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: auto; }
        label { display: block; margin: 10px 0 5px; }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 8px; margin-bottom: 15px;
        }
        input[type="submit"] {
            background: #2980b9; color: white; padding: 10px 20px;
            border: none; border-radius: 5px; cursor: pointer;
        }
        a { text-decoration: none; color: #2c3e50; display: block; margin-top: 15px; }
    </style>
</head>
<body>
    <h2>Editar Producto</h2>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" required>
        <label>Descripción:</label>
        <input type="text" name="descripcion" value="<?= $producto['descripcion'] ?>" required>
        <label>Cantidad:</label>
        <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>" required>
        <label>Precio:</label>
        <input type="number" name="precio" step="0.01" value="<?= $producto['precio'] ?>" required>
        <input type="submit" value="Actualizar">
        <a href="inventario.php">← Volver</a>
    </form>
</body>
</html>
