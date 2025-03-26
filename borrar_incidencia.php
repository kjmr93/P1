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

// Verificar si se recibió el valor de 'incidencia'
if (isset($_POST['incidencia']) && !empty($_POST['incidencia'])) {
    $incidencia = $conn->real_escape_string($_POST['incidencia']);

    // Eliminar la incidencia de la tabla
    $sql = "DELETE FROM incidencias WHERE incidencia = '$incidencia'";

    if ($conn->query($sql) === TRUE) {
        // Redirigir de vuelta a incidencias.php después de eliminar
        $conn->close();
        header("Location: incidencias.php");
        exit();
    } else {
        echo "Error al eliminar la incidencia: " . $conn->error;
    }
} else {
    echo "No se recibió una incidencia válida para eliminar.";
}

// Cerrar la conexión
$conn->close();

// Redirigir de vuelta a incidencias.php si no se recibió una incidencia válida
header("Location: incidencias.php");
exit();
?>