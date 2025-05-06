<?php
require 'vendor/autoload.php'; // Asegúrate de tener PHPSpreadsheet instalado
use PhpOffice\PhpSpreadsheet\IOFactory;

// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_usuarios'])) {
    $archivo = $_FILES['archivo_usuarios']['tmp_name'];

    try {
        // Cargar el archivo .xlsx
        $spreadsheet = IOFactory::load($archivo);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Ignorar la primera línea (encabezados)
        unset($rows[0]);

        // Preparar la consulta SQL para actualizar los datos
        $updateStmt = $conn->prepare("
            UPDATE usuaris 
            SET nom = ?, cognoms = ?, cognoms2 = ?, curs = ?, clase = ?
            WHERE nomusuari = ?
        ");

        // Preparar la consulta SQL para insertar nuevos datos
        $insertStmt = $conn->prepare("
            INSERT INTO usuaris (nomusuari, nom, cognoms, cognoms2, curs, clase, restriccio)
            VALUES (?, ?, ?, ?, ?, ?, 0)
        ");

        // Procesar cada fila del archivo
        foreach ($rows as $row) {
            // Validar que las columnas requeridas no estén vacías
            if (empty($row[0]) || empty($row[3])) {
                // Si 'nom' o 'nomusuari' están vacíos, omitir esta fila
                continue;
            }

            $nomusuari = str_replace('@alu.edu.gva.es', '', $row[3]); // Eliminar "@alu.edu.gva.es" de la cuarta columna
            $nom = $row[0]; // Primera columna
            $cognoms = $row[1] ?? ''; // Segunda columna (puede estar vacía)
            $cognoms2 = $row[2] ?? ''; // Tercera columna (puede estar vacía)
            $curs = $row[4] ?? ''; // Quinta columna (puede estar vacía)
            $clase = $row[5] ?? ''; // Sexta columna (puede estar vacía)

            // Verificar si el usuario ya existe en la base de datos
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM usuaris WHERE nomusuari = ?");
            $checkStmt->bind_param('s', $nomusuari);
            $checkStmt->execute();
            $checkStmt->bind_result($exists);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($exists > 0) {
                // Si el usuario existe, actualizar los datos
                $updateStmt->bind_param('ssssss', $nom, $cognoms, $cognoms2, $curs, $clase, $nomusuari);
                $updateStmt->execute();
            } else {
                // Si el usuario no existe, insertar una nueva fila con restriccio = 0
                $insertStmt->bind_param('ssssss', $nomusuari, $nom, $cognoms, $cognoms2, $curs, $clase);
                $insertStmt->execute();
            }
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