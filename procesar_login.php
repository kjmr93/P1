<?php
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

// Consultar la base de datos para verificar el usuario y la contraseña
$sql = "SELECT usuario, password, administrador FROM profesorado WHERE usuario = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $contrasena);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuario encontrado
    $row = $result->fetch_assoc();
    $es_administrador = $row['administrador'] == 0; // Verificar si es administrador

    // Crear cookies para identificar al usuario
    setcookie("usuario", $usuario, time() + (600), "/"); // Cookie válida por 10 minutos
    setcookie("es_administrador", $es_administrador ? "1" : "0", time() + (600), "/");

    // Redirigir al usuario a la página principal
    header("Location: inicio.php");
    exit();
} else {
    // Usuario no encontrado o credenciales incorrectas
    header("Location: login.php?error=Usuario%20o%20contraseña%20incorrectos.");
    exit();
}

$stmt->close();
?>