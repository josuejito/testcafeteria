<?php
$serverName = "tcp:cafeteriahn.database.windows.net,1433";
$connectionOptions = [
    "Database" => "cafeteria",
    "Uid" => "josuejorge@cafeteriahn",
    "PWD" => "Barcelona25"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) die(print_r(sqlsrv_errors(), true));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productos = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];

    // Crear factura
    $facturaSQL = "INSERT INTO Facturas DEFAULT VALUES";
    $facturaStmt = sqlsrv_query($conn, $facturaSQL);
    if (!$facturaStmt) die(print_r(sqlsrv_errors(), true));

    // Obtener ID de la nueva factura
    $facturaIdQuery = sqlsrv_query($conn, "SELECT SCOPE_IDENTITY() AS id");
    $facturaRow = sqlsrv_fetch_array($facturaIdQuery, SQLSRV_FETCH_ASSOC);
    $idFactura = $facturaRow['id'];

    for ($i = 0; $i < count($productos); $i++) {
        // Validar entrada
        if (!isset($productos[$i]) || !isset($cantidades[$i]) || $cantidades[$i] <= 0) continue;

        $idProducto = (int)$productos[$i];
        $cantidad = (int)$cantidades[$i];

        // Obtener precio
        $precioSQL = "SELECT precio FROM Productos WHERE id = ?";
        $precioStmt = sqlsrv_query($conn, $precioSQL, [$idProducto]);
        if (!$precioStmt || !sqlsrv_has_rows($precioStmt)) continue;

        $row = sqlsrv_fetch_array($precioStmt, SQLSRV_FETCH_ASSOC);
        $precioUnitario = floatval($row['precio']);
        $total = $precioUnitario * $cantidad;

        sqlsrv_free_stmt($precioStmt);

        // Insertar venta
        $insertVenta = "INSERT INTO Ventas (producto_id, cantidad, total, factura_id) VALUES (?, ?, ?, ?)";
        $params = [$idProducto, $cantidad, $total, $idFactura];
        $insertStmt = sqlsrv_query($conn, $insertVenta, $params);

        if (!$insertStmt) {
            echo "Error al registrar producto ID $idProducto<br>";
            print_r(sqlsrv_errors());
        }
    }

    // Redirigir después de insertar
    header("Location: registro_ventas.php");
    exit();
}

// Obtener productos
$productos = sqlsrv_query($conn, "SELECT id, nombre FROM Productos");
$productoOptions = [];
while ($p = sqlsrv_fetch_array($productos, SQLSRV_FETCH_ASSOC)) {
    $productoOptions[] = $p;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        form { background: white; padding: 20px; border-radius: 10px; max-width: 700px; margin: auto; }
        label, select, input[type="number"] { margin: 5px; display: inline-block; }
        .fila { margin-bottom: 10px; }
        .fila select, .fila input { width: 200px; }
        .add-btn { background: #3498db; color: white; padding: 5px 10px; border: none; border-radius: 5px; margin: 10px 0; cursor: pointer; }
        .submit-btn { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-top: 10px; cursor: pointer; }
    </style>
    <script>
        function agregarFila() {
            const contenedor = document.getElementById("productos");
            const div = document.createElement("div");
            div.className = "fila";
            div.innerHTML = '<?= generarFilaJS($productoOptions) ?>';
            contenedor.appendChild(div);
        }
    </script>
</head>
<body>
    <h2>Registrar Venta (Múltiples Productos)</h2>
    <form method="POST">
        <div id="productos">
            <div class="fila">
                <?= generarFilaHTML($productoOptions) ?>
            </div>
        </div>
        <button type="button" class="add-btn" onclick="agregarFila()">+ Agregar otro producto</button><br>
        <input type="submit" value="Registrar Venta" class="submit-btn">
    </form>
</body>
</html>

<?php
// Función para HTML inicial
function generarFilaHTML($productos) {
    $html = '<select name="producto_id[]">';
    foreach ($productos as $p) {
        $html .= "<option value='{$p['id']}'>{$p['nombre']}</option>";
    }
    $html .= '</select>';
    $html .= '<input type="number" name="cantidad[]" min="1" required>';
    return $html;
}

// Función para insertar dinámicamente con JS
function generarFilaJS($productos) {
    $html = "<select name='producto_id[]'>";
    foreach ($productos as $p) {
        $html .= "<option value='{$p['id']}'>{$p['nombre']}</option>";
    }
    $html .= "</select>";
    $html .= "<input type='number' name='cantidad[]' min='1' required>";
    return addslashes($html); // 🔒 Escapa para JS
}
?>
