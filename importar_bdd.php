<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = ""; // Deja vacío si no hay contraseña
$dbname = "pruebas";

// Verificar si se ha subido un archivo
if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
    // Ruta temporal del archivo subido
    $file_tmp = $_FILES['sql_file']['tmp_name'];

    // Ruta completa del comando mysql (ajusta según tu sistema)
    $mysql_path = "C:\\xampp\\mysql\\bin\\mysql.exe"; // Ruta para sistemas Windows

    // Crear conexión a la base de datos para eliminar todas las tablas
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Eliminar todas las tablas de la base de datos
    $conn->query("SET FOREIGN_KEY_CHECKS = 0"); // Desactivar restricciones de claves foráneas
    if ($result = $conn->query("SHOW TABLES")) {
        while ($row = $result->fetch_array()) {
            $conn->query("DROP TABLE " . $row[0]);
        }
    }
    $conn->query("SET FOREIGN_KEY_CHECKS = 1"); // Reactivar restricciones de claves foráneas
    $conn->close();

    // Comando para importar la base de datos con manejo de contraseña vacía
    if ($password === "") {
        $command = "$mysql_path -u $username $dbname < $file_tmp";
    } else {
        $command = "$mysql_path -u $username -p'$password' $dbname < $file_tmp";
    }

    // Ejecutar el comando
    exec($command . " 2>&1", $output, $return_var);

    if ($return_var === 0) {
        // Redirigir a inicio.php con mensaje de éxito
        header("Location: inicio.php?status=success&message=Base%20de%20datos%20importada%20correctamente");
        exit();
    } else {
        // Mostrar el error para depuración
        echo "Error al importar la base de datos:<br>";
        echo implode("<br>", $output);
        exit();
    }
} else {
    // Redirigir a inicio.php con mensaje de error si no se subió un archivo
    header("Location: inicio.php?status=error&message=No%20se%20subió%20ningún%20archivo");
    exit();
}
?>