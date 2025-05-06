<?php
// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Verificar si se recibieron las fechas
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

if ($fecha_inicio && $fecha_fin) {
    // Convertir las fechas para incluir el rango completo del día
    $fecha_inicio .= " 00:00:00";
    $fecha_fin .= " 23:59:59";

    // Preparar la consulta para borrar los datos en el rango de fechas
    $sql = "DELETE FROM historial WHERE fecha_conexion BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);

    if ($stmt->execute()) {
        echo "Datos eliminados correctamente entre $fecha_inicio y $fecha_fin.";
    } else {
        echo "Error al eliminar los datos: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Si no se reciben fechas, borrar todos los datos de la tabla
    $sql = "DELETE FROM historial";

    if ($conn->query($sql) === TRUE) {
        echo "Todos los datos de la tabla 'historial' han sido eliminados.";
    } else {
        echo "Error al eliminar los datos: " . $conn->error;
    }
}

$conn->close();

header("Location: historial.php");
exit;
?>