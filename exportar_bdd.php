<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Nombre del archivo de respaldo
$backup_file = '/tmp/backup_' . date('Y-m-d_H-i-s') . '.sql';

// Ruta completa de mysqldump
//$mysqldump_path = "C:\\xampp\\mysql\\bin\\mysqldump.exe"; // Ruta para sistemas Windows
$mysqldump_path = "/usr/bin/mysqldump"; // Ruta para sistemas Linux

// Comando para exportar la base de datos con manejo de contraseña vacía
if ($password === "") {
    $command = "$mysqldump_path -u $username --result-file=$backup_file $dbname";
} else {
    $command = "$mysqldump_path -u $username -p'$password' --result-file=$backup_file $dbname";
}

// Ejecutar el comando
exec($command . " 2>&1", $output, $return_var);

if ($return_var === 0) {
    // Descargar el archivo de respaldo
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $backup_file . '"');
    readfile($backup_file);

    // Eliminar el archivo después de la descarga
    unlink($backup_file);
    exit();
} else {
    // Mostrar mensaje de error para depuración
    echo "Error al exportar la base de datos:<br>";
    echo implode("<br>", $output);
    exit();
}
?>