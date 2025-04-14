<?php
require 'vendor/autoload.php'; // Asegúrate de tener PHPSpreadsheet instalado
use PhpOffice\PhpSpreadsheet\IOFactory;

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_usuarios'])) {
    $archivo = $_FILES['archivo_usuarios']['tmp_name'];

    try {
        // Cargar el archivo .xlsx
        $spreadsheet = IOFactory::load($archivo);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Ignorar la primera línea (encabezados)
        unset($rows[0]);

        // Preparar la consulta SQL
        $stmt = $conn->prepare("
            INSERT INTO usuaris (nomusuari, nom, cognoms, cognoms2, curs, clase, restriccio)
            VALUES (?, ?, ?, ?, ?, ?, 0)
        ");

        // Procesar cada fila del archivo
        foreach ($rows as $row) {
            $nomusuari = str_replace('@alu.edu.gva.es', '', $row[3]); // Eliminar "@alu.edu.gva.es" de la cuarta columna
            $nom = $row[0]; // Primera columna
            $cognoms = $row[1]; // Segunda columna
            $cognoms2 = $row[2]; // Tercera columna
            $curs = $row[4]; // Quinta columna
            $clase = $row[5]; // Sexta columna

            // Ejecutar la consulta
            $stmt->bind_param('ssssss', $nomusuari, $nom, $cognoms, $cognoms2, $curs, $clase);
            $stmt->execute();
        }

        // Redirigir a usuarios.php si todo salió bien
        header('Location: usuarios.php');
        exit;
    } catch (Exception $e) {
        // Mostrar el error si algo falla
        die('Error al procesar el archivo: ' . $e->getMessage());
    }
} else {
    die('No se ha subido ningún archivo.');
}
?>