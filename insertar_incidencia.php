<?php
// Conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Datos de la petición
$incidencia = $_POST['incidencia'];
$usuario = $_POST['usuario'];
$equipo = $_POST['equipo'];
$clase = $_POST['clase'];
$restriccion_equipo = $_POST['restriccion_equipo'];
$restriccion_usuario = $_POST['restriccion_usuario'];

// Obtener la fecha y hora actual del sistema
$fecha_completa = date('Y-m-d H:i:s'); // Formato compatible con MySQL: YYYY-MM-DD HH:MM:SS

// Insertar datos en la base de datos
$sql = "INSERT INTO incidencias (incidencia, usuario, equipo, clase, restriccion_equipo, restriccion_usuario, fecha)
VALUES ('$incidencia', '$usuario', '$equipo', '$clase', '$restriccion_equipo', '$restriccion_usuario', '$fecha_completa')";

if ($conn->query($sql) === TRUE) {
    echo "Incidencia insertada correctamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

// Redirigir de vuelta a la página de incidencias
header("Location: incidencias.php");
exit();
?>