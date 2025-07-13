<?php
// Validar que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contraseña'];

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

    $sql = "SELECT * FROM Usuarios WHERE usuario = ? AND contrasena = ?";
    $params = array($usuario, $contrasena);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($stmt)) {
        // Redirigir a página protegida
        header("Location: inventario.php");
        exit();
    } else {
        // Regresar al login con mensaje de error
        header("Location: index.php?error=1");
        exit();
    }
}
?>
