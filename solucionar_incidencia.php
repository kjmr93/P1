<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el id de la incidencia a actualizar
$id = $_POST['id'];

// Actualizar el estado de la incidencia con el id proporcionado
$sql = "UPDATE incidencias SET estado = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Incidencia marcada como solucionada correctamente";
} else {
    echo "Error al actualizar la incidencia: " . $conn->error;
}

$stmt->close();
$conn->close();

// Redirigir de vuelta a la página de incidencias
header("Location: incidencias.php");
exit();
?>