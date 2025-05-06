<?php
// Verificar si el usuario ha iniciado sesión
if (!isset($_COOKIE['usuario'])) {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20primero.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Consulta para contar las filas de la tabla "incidencias" con estado = 0
$sql = "SELECT COUNT(*) AS total FROM incidencias WHERE estado = 0";
$result = $conn->query($sql);

$alert_message = "No hay alertas pendientes";
$alert_class = "alerta-sin-alertas";

if ($result && $row = $result->fetch_assoc()) {
    $total_incidencias = $row['total'];
    if ($total_incidencias > 0) {
        $alert_message = "ALERTA: Hay un total de $total_incidencias incidencias pendientes";
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
            position: relative;
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

        .boton2 {
            background-color: #77dd77;
            color: #333;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            flex: 1 1 calc(50% - 20px);
            max-width: calc(50% - 20px);
        }

        .boton2:hover {
            background-color: #5ca65c;
        }

        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: white;
            color: #4a90e2;
            border: 1px solid #4a90e2;
            padding: 5px 10px;
            text-decoration: none;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #4a90e2;
            color: white;
            border: 1px solid white;
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
    <h1>
        Página de Inicio
        <a href="logout.php" class="logout-button">Log out</a>
    </h1>
    <div class="alerta <?php echo $alert_class; ?>">
        <?php echo $alert_message; ?>
    </div>
    <div class="botones-container">
        <button class="boton" onclick="window.location.href='historial.php'">Historial de Conexiones</button>
        <button class="boton" onclick="window.location.href='incidencias.php'">Historial de Incidencias</button>
        <button class="boton2" onclick="window.location.href='historial2.php'">Borrar Conexiones</button>
        <button class="boton2" onclick="window.location.href='incidencias2.php'">Borrar Incidencias</button>
        <button class="boton" onclick="window.location.href='equipos.php'">Datos de Equipos</button>
        <button class="boton" onclick="window.location.href='usuarios.php'">Datos de Usuarios</button>
        <button class="boton" onclick="window.location.href='antenas.php'">Datos de Antenas</button>
        <button class="boton" onclick="window.location.href='ultimas_conexiones.php'">Últimas Conexiones</button>
        <button class="boton2" onclick="window.location.href='exportar_bdd.php'">Exportar BDD</button>
        <button class="boton2" onclick="document.getElementById('importar-form').classList.toggle('hidden')">Importar BDD</button>
        <button class="boton2" onclick="window.location.href='profesorado.php'">Profesorado</button>    
    </div>
    <form id="importar-form" class="hidden" method="POST" action="importar_bdd.php" enctype="multipart/form-data">
        <input type="file" name="sql_file" accept=".sql" required>
        <button type="submit" class="boton2">Subir y Restaurar</button>
    </form>
</body>
</html>