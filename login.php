<?php
session_start(); // Necesario para guardar sesión de usuario
// Evitar cache para que no se pueda usar flecha atrás para ver páginas protegidas
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

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

    // Consulta solo el usuario
    $sql = "SELECT * FROM Usuarios WHERE usuario = ?";
    $params = array($usuario);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // ⚠️ Comparación directa (para pruebas)
        if ($contrasena === $row['contrasena']) {
            $_SESSION['usuario'] = $usuario;
            header("Location: inventario.php");
            exit();
        } else {
            header("Location: index.php?error=1"); // Contraseña incorrecta
            exit();
        }
    } else {
        header("Location: index.php?error=1"); // Usuario no encontrado
        exit();
    }
}
?>


