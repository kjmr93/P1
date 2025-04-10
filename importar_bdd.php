<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

// Verificar si se ha subido un archivo
if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
    // Ruta temporal del archivo subido
    $file_tmp = $_FILES['sql_file']['tmp_name'];

    // Ruta completa del comando mysql (ajusta según tu sistema)
    $mysql_path = "/usr/bin/mysql"; // Cambia esta ruta si es necesario

    // Comando para importar la base de datos
    $command = "$mysql_path -u $username -p$password $dbname < $file_tmp";

    // Ejecutar el comando
    exec($command . " 2>&1", $output, $return_var);

    if ($return_var === 0) {
        // Redirigir a inicio.php con mensaje de éxito
        header("Location: inicio.php?status=success&message=Base%20de%20datos%20importada%20correctamente");
        exit();
    } else {
        // Redirigir a inicio.php con mensaje de error
        header("Location: inicio.php?status=error&message=Error%20al%20importar%20la%20base%20de%20datos");
        exit();
    }
} else {
    // Redirigir a inicio.php con mensaje de error si no se subió un archivo
    header("Location: inicio.php?status=error&message=No%20se%20subió%20ningún%20archivo");
    exit();
}
?>