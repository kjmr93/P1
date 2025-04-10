<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para contar las filas de la tabla "incidencias"
$sql = "SELECT COUNT(*) AS total FROM incidencias";
$result = $conn->query($sql);

$alert_message = "No hay alertas pendientes";
$alert_class = "alerta-sin-alertas";

if ($result && $row = $result->fetch_assoc()) {
    $total_incidencias = $row['total'];
    if ($total_incidencias > 0) {
        $alert_message = "ALERTA: Hay un total de $total_incidencias incidencias";
        $alert_class = "alerta-con-alertas";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
            margin: 0;
        }
        .alerta {
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            text-align: center;
            width: 80%;
            font-size: 18px;
        }
        .alerta-sin-alertas {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alerta-con-alertas {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .botones-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px auto;
            width: 80%;
        }
        .boton {
            background-color: #4a90e2;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            flex: 1 1 calc(50% - 20px);
            max-width: calc(50% - 20px);
        }
        .boton:hover {
            background-color: #357ab8;
        }
        .hidden {
            display: none;
        }
        #importar-form {
            margin-top: 10px;
            text-align: center;
        }
        #importar-form input[type="file"] {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Página de Inicio</h1>
    <div class="alerta <?php echo $alert_class; ?>">
        <?php echo $alert_message; ?>
    </div>
    <div class="botones-container">
        <button class="boton" onclick="window.location.href='historial.php'">Historial de Conexiones</button>
        <button class="boton" onclick="window.location.href='incidencias.php'">Historial de Incidencias</button>
        <button class="boton" onclick="window.location.href='equipos.php'">Datos de Equipos</button>
        <button class="boton" onclick="window.location.href='usuarios.php'">Datos de Usuarios</button>
        <button class="boton" onclick="window.location.href='antenas.php'">Datos de Antenas</button>
        <button class="boton" onclick="window.location.href='ultimas_conexiones.php'">Últimas Conexiones</button>
        <button class="boton" onclick="window.location.href='funcionalidad7.php'">Botón 7</button>
        <button class="boton" onclick="window.location.href='funcionalidad8.php'">Botón 8</button>
        <button class="boton" onclick="window.location.href='exportar_bdd.php'">Exportar BDD</button>
        <button class="boton" onclick="document.getElementById('importar-form').classList.toggle('hidden')">Importar BDD</button>
    </div>
    <form id="importar-form" class="hidden" method="POST" action="importar_bdd.php" enctype="multipart/form-data">
        <input type="file" name="sql_file" accept=".sql" required>
        <button type="submit" class="boton">Subir y Restaurar</button>
    </form>
</body>
</html>