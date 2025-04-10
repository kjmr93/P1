<?php
$backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$command = "mysqldump -u root -p pruebas > $backup_file";

// Ejecutar el comando
exec($command, $output, $return_var);

if ($return_var === 0) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $backup_file . '"');
    readfile($backup_file);
    unlink($backup_file); // Eliminar el archivo después de la descarga
} else {
    echo "Error al exportar la base de datos.";
}
?>