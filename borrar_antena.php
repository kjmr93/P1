<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Obtener el ID del formulario
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar que el ID sea válido
if ($id <= 0) {
    header("Location: antenas.php?error=ID%20inválido.");
    exit();
}

// Eliminar la antena de la tabla "antenas"
$sql = "DELETE FROM antenas WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirigir con mensaje de éxito
    header("Location: antenas.php?success=Antena%20eliminada%20correctamente.");
    exit();
} else {
    // Mostrar mensaje de error si la ejecución falla
    die("Error al ejecutar la consulta: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>