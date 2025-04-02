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

// Inicializar variables
$dias = isset($_GET['dias']) ? (int)$_GET['dias'] : 0;
$resultados = [];

// Si se ha seleccionado un número de días, realizar la consulta
if ($dias > 0) {
    $fecha_limite = date('Y-m-d H:i:s', strtotime("-$dias days"));

    // Consulta para obtener los usuarios cuya última conexión sea mayor a la fecha límite
    $sql = "
        SELECT nomusuari, ultima_conexion
        FROM (
            SELECT nomusuari, MAX(fecha_conexion) AS ultima_conexion
            FROM historial
            GROUP BY nomusuari
        ) AS subconsulta
        WHERE ultima_conexion < ?
        ORDER BY ultima_conexion DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha_limite);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $resultados[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Inactivos</title>
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
        h1 .inicio-button {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: white;
            color: #4a90e2;
            border: 1px solid #4a90e2;
            padding: 5px 10px;
            text-decoration: none;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        h1 .inicio-button:hover {
            background-color: #4a90e2;
            color: white;
            border: 1px solid white;
        }
        form {
            margin: 20px auto;
            text-align: center;
        }
        input[type="number"] {
            width: 100px;
            padding: 8px;
            margin: 4px 0;
            box-sizing: border-box;
        }
        button {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #357ab8;
        }
        table {
            width: auto;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            width: 250px;
        }
        th {
            background-color: #4a90e2;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h1>
        <a href="inicio.php" class="inicio-button">Inicio</a>
        Últimas Conexiones
    </h1>
    <form method="GET" action="ultimas_conexiones.php">
        <label for="dias">Días sin conexión:</label>
        <input type="number" id="dias" name="dias" min="1" max="999" value="<?php echo htmlspecialchars($dias); ?>" required>
        <button type="submit">Buscar</button>
        <button type="button" onclick="window.location.href='ultimas_conexiones.php'">Limpiar filtros</button>
    </form>

    <?php if ($dias > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre de Usuario</th>
                    <th>Última Fecha de Conexión</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultados)): ?>
                    <?php foreach ($resultados as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nomusuari']); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($row['ultima_conexion']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No se encontraron usuarios inactivos con más de <?php echo htmlspecialchars($dias); ?> días sin conexión.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>