<?php
// Conexión
$serverName = "tcp:cafeteriahn.database.windows.net,1433";
$connectionOptions = [
    "Database" => "cafeteria",
    "Uid" => "josuejorge@cafeteriahn",
    "PWD" => "Barcelona25"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) die(print_r(sqlsrv_errors(), true));

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];

    // Obtener precio del producto
    $stmt = sqlsrv_query($conn, "SELECT precio FROM Productos WHERE id = ?", [$producto_id]);
    $producto = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $precio_unitario = $producto['precio'];
    $total = $precio_unitario * $cantidad;

    // Insertar venta
    $sql = "INSERT INTO Ventas (producto_id, cantidad, total) VALUES (?, ?, ?)";
    $params = [$producto_id, $cantidad, $total];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        header("Location: registro_ventas.php");
        exit();
    } else {
        echo "Error al registrar venta.";
    }
}

// Obtener productos para dropdown
$productos = sqlsrv_query($conn, "SELECT id, nombre FROM Productos");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        form { background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: auto; }
        label { display: block; margin: 10px 0 5px; }
        select, input[type="number"] {
            width: 100%; padding: 8px; margin-bottom: 15px;
        }
        input[type="submit"] {
            background: #27ae60; color: white; padding: 10px 20px;
            border: none; border-radius: 5px; cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Registrar Venta</h2>
    <form method="POST">
        <label>Producto:</label>
        <select name="producto_id" required>
            <?php while ($p = sqlsrv_fetch_array($productos, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" required>

        <input type="submit" value="Registrar Venta">
    </form>
</body>
</html>
