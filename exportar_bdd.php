<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

// Nombre del archivo de respaldo
$backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

// Ruta completa de mysqldump (ajusta según tu sistema)
$mysqldump_path = "/usr/bin/mysqldump"; // Cambia esta ruta si es necesario

// Comando para exportar la base de datos
$command = "$mysqldump_path -u $username -p$password $dbname > $backup_file";

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