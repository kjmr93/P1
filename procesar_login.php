<?php
// filepath: /workspaces/P1/procesar_login.php

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

// Verificar si los campos están vacíos
if (empty($usuario) || empty($contrasena)) {
    header("Location: login.php?error=Por%20favor,%20complete%20todos%20los%20campos.");
    exit();
}

// Consultar la base de datos para verificar el usuario
$sql = "SELECT usuario, password, administrador FROM profesorado WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuario encontrado
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    // Verificar la contraseña
    if (password_verify($contrasena, $hashed_password)) {
        $es_administrador = $row['administrador'] == 0; // Verificar si es administrador

        // Crear cookies para identificar al usuario
        setcookie("usuario", $usuario, time() + (600), "/"); // Cookie válida por 10 minutos
        setcookie("es_administrador", $es_administrador ? "1" : "0", time() + (600), "/");

        // Redirigir al usuario a la página principal
        header("Location: inicio.php");
        exit();
    } else {
        // Contraseña incorrecta
        header("Location: login.php?error=Usuario%20o%20contraseña%20incorrectos.");
        exit();
    }
} else {
    // Usuario no encontrado
    header("Location: login.php?error=Usuario%20o%20contraseña%20incorrectos.");
    exit();
}

$stmt->close();
$conn->close();
?>