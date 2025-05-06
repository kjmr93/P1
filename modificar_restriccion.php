<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Obtener datos del formulario
$nomusuari = $_POST['nomusuari'];
$nueva_restriccion = $_POST['nueva_restriccion'];

// Actualizar la restricción del usuario
$sql_update = "UPDATE usuaris SET restriccio = ? WHERE nomusuari = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("is", $nueva_restriccion, $nomusuari);

if ($stmt->execute()) {
    echo "Restricción actualizada correctamente.";
} else {
    echo "Error al actualizar la restricción: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirigir de vuelta a la página de usuarios
header("Location: usuarios.php");
exit();
?>