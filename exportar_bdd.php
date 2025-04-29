<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = ""; // Deja vacío si no hay contraseña
$dbname = "pruebas";

// Aumentar el tiempo de ejecución del script
set_time_limit(300); // 5 minutos

// Nombre del archivo de respaldo
$backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

// Ruta completa de mysqldump (ajusta según tu sistema)
$mysqldump_path = "C:\\xampp\\mysql\\bin\\mysqldump.exe"; // Ruta para sistemas Windows

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