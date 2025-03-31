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

// Obtener el id de la incidencia a borrar
$id = $_POST['id'];

// Borrar la incidencia con el id proporcionado
$sql = "DELETE FROM incidencias WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Incidencia borrada correctamente";
} else {
    echo "Error al borrar la incidencia: " . $conn->error;
}

$stmt->close();
$conn->close();

// Redirigir de vuelta a la página de incidencias
header("Location: incidencias.php");
exit();
?>