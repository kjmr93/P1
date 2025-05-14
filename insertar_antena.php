<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Obtener los datos del formulario
$aula = isset($_POST['aula']) ? trim($_POST['aula']) : '';
$mac = isset($_POST['mac']) ? trim($_POST['mac']) : '';

// Validar que los campos "aula" y "mac" no estén vacíos
if (empty($aula) || empty($mac)) {
    header("Location: antenas.php?error=Los%20campos%20Aula%20y%20MAC%20son%20obligatorios.");
    exit();
}

// Insertar los datos en la tabla "antenas"
$sql = "INSERT INTO antenas (aula, mac) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("ss", $aula, $mac);

if ($stmt->execute()) {
    // Redirigir con mensaje de éxito
    header("Location: antenas.php?success=Antena%20añadida%20correctamente.");
    exit();
} else {
    // Mostrar mensaje de error si la ejecución falla
    die("Error al ejecutar la consulta: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>