<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Obtener los datos del formulario
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$rol = isset($_POST['rol']) ? $_POST['rol'] : '1'; // Por defecto, rol "Usuario"

// Validar que el ID sea válido
if ($id <= 0) {
    header("Location: profesorado.php?error=ID%20inválido.");
    exit();
}

// Validar que las contraseñas coincidan
if ($password !== $confirm_password) {
    header("Location: profesorado.php?error=Las%20contraseñas%20no%20coinciden.");
    exit();
}

// Validar que el campo "usuario" no esté vacío
if (empty($usuario)) {
    header("Location: profesorado.php?error=El%20campo%20usuario%20es%20obligatorio.");
    exit();
}

// Cifrar la contraseña
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// Actualizar los datos en la tabla "profesorado"
$sql = "UPDATE profesorado SET usuario = ?, password = ?, administrador = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("ssii", $usuario, $password_hashed, $rol, $id);

if ($stmt->execute()) {
    // Redirigir con mensaje de éxito
    header("Location: profesorado.php?success=Usuario%20actualizado%20correctamente.");
    exit();
} else {
    // Mostrar mensaje de error si la ejecución falla
    die("Error al ejecutar la consulta: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>