<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

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

    $sql = "DELETE FROM Productos WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);

    if ($stmt) {
        header("Location: inventario.php");
        exit();
    } else {
        echo "Error al eliminar.";
    }
} else {
    echo "ID no proporcionado.";
}
?>
