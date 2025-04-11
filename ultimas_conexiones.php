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
$data_restauracio = isset($_GET['data_restauracio']) ? $_GET['data_restauracio'] : '';
$restriccion_equipo = isset($_GET['restriccion_equipo']) ? $_GET['restriccion_equipo'] : '';
$restriccion_usuario = isset($_GET['restriccion_usuario']) ? $_GET['restriccion_usuario'] : '';
$resultados = [];

// Construir la consulta con los filtros
if ($dias > 0) {
    $fecha_limite = date('Y-m-d H:i:s', strtotime("-$dias days"));

    $sql = "
        SELECT historial.nomusuari, MAX(historial.fecha_conexion) AS ultima_conexion, historial.restriccio, historial.restriccio_usuari
        FROM historial
        WHERE historial.fecha_conexion < ?
    ";

    // Agregar filtro por Fecha de Restauración
    if (!empty($data_restauracio)) {
        $sql .= " AND DATE(historial.data_restauracio) = ?";
    }

    // Agregar filtro por Restricción Equipo
    if ($restriccion_equipo !== '') {
        $sql .= " AND historial.restriccio = ?";
    }

    // Agregar filtro por Restricción Usuario
    if ($restriccion_usuario !== '') {
        $sql .= " AND historial.restriccio_usuari = ?";
    }

    $sql .= " GROUP BY historial.nomusuari ORDER BY ultima_conexion DESC";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Vincular parámetros dinámicamente
    $params = [];
    $types = 's'; // Para el parámetro de fecha límite
    $params[] = $fecha_limite;

    if (!empty($data_restauracio)) {
        $types .= 's';
        $params[] = $data_restauracio;
    }
    if ($restriccion_equipo !== '') {
        $types .= 'i';
        $params[] = $restriccion_equipo;
    }
    if ($restriccion_usuario !== '') {
        $types .= 'i';
        $params[] = $restriccion_usuario;
    }

    $stmt->bind_param($types, ...$params);
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
    <title>Últimas Conexiones</title>
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
        input[type="number"], input[type="date"], select {
            width: 150px;
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
        <label for="data_restauracio">Fecha de Restauración:</label>
        <input type="date" id="data_restauracio" name="data_restauracio" value="<?php echo htmlspecialchars($data_restauracio); ?>">
        <label for="restriccion_equipo">Restricción Equipo:</label>
        <select id="restriccion_equipo" name="restriccion_equipo">
            <option value="">Seleccionar</option>
            <option value="0" <?php echo $restriccion_equipo === "0" ? 'selected' : ''; ?>>0</option>
            <option value="1" <?php echo $restriccion_equipo === "1" ? 'selected' : ''; ?>>1</option>
            <option value="2" <?php echo $restriccion_equipo === "2" ? 'selected' : ''; ?>>2</option>
        </select>
        <label for="restriccion_usuario">Restricción Usuario:</label>
        <select id="restriccion_usuario" name="restriccion_usuario">
            <option value="">Seleccionar</option>
            <option value="0" <?php echo $restriccion_usuario === "0" ? 'selected' : ''; ?>>0</option>
            <option value="1" <?php echo $restriccion_usuario === "1" ? 'selected' : ''; ?>>1</option>
            <option value="2" <?php echo $restriccion_usuario === "2" ? 'selected' : ''; ?>>2</option>
        </select>
        <button type="submit">Buscar</button>
        <button type="button" onclick="window.location.href='ultimas_conexiones.php'">Limpiar filtros</button>
    </form>

    <?php if ($dias > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre de Usuario</th>
                    <th>Última Fecha de Conexión</th>
                    <th>Restricción Equipo</th>
                    <th>Restricción Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultados)): ?>
                    <?php foreach ($resultados as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nomusuari']); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($row['ultima_conexion']))); ?></td>
                            <td><?php echo htmlspecialchars($row['restriccio']); ?></td>
                            <td><?php echo htmlspecialchars($row['restriccio_usuari']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No se encontraron usuarios inactivos con más de <?php echo htmlspecialchars($dias); ?> días sin conexión.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>