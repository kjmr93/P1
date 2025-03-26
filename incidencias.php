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

// Obtener valores únicos de la columna 'restriccion'
$restriccion_sql = "SELECT DISTINCT restriccion FROM incidencias";
$restriccion_result = $conn->query($restriccion_sql);
$restriccion_options = [];
if ($restriccion_result->num_rows > 0) {
    while ($row = $restriccion_result->fetch_assoc()) {
        $restriccion_options[] = $row['restriccion'];
    }
}

// Paginación
$results_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Filtros
$filters = [
    'incidencia' => isset($_GET['filter-incidencia']) ? $_GET['filter-incidencia'] : '',
    'usuario' => isset($_GET['filter-usuario']) ? $_GET['filter-usuario'] : '',
    'restriccion' => isset($_GET['filter-restriccion']) ? $_GET['filter-restriccion'] : '',
    'fecha' => isset($_GET['filter-fecha']) ? $_GET['filter-fecha'] : ''
];

// Filtros de fecha
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'incidencia';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';

// Construir la consulta SQL con paginación, filtros y ordenación
$sql = "SELECT incidencia, usuario, restriccion, fecha FROM incidencias WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') {
        $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_fin_adjusted = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
    $sql .= " AND fecha BETWEEN '" . $conn->real_escape_string($fecha_inicio) . "' AND '" . $conn->real_escape_string($fecha_fin_adjusted) . "'";
}

$sql .= " ORDER BY $order_by $order_dir LIMIT $start_from, $results_per_page";

// Ejecutar la consulta y verificar errores
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "SELECT COUNT(*) FROM incidencias WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') {
        $total_sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_fin_adjusted = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
    $total_sql .= " AND fecha BETWEEN '" . $conn->real_escape_string($fecha_inicio) . "' AND '" . $conn->real_escape_string($fecha_fin_adjusted) . "'";
}

$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $results_per_page);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Incidencias</title>
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
        table {
            width: auto;
            border-collapse: collapse;
            margin: 20px 0;
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
        .pagination {
            text-align: center;
            margin: 20px 0;
        }
        .pagination a {
            color: #4a90e2;
            padding: 8px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
        form {
            margin: 20px;
        }
        input[type="text"], input[type="date"], select {
            width: 100%;
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
    </style>
</head>
<body>
    <h1>
        <a href="inicio.php" class="inicio-button">Inicio</a>
        Historial de Incidencias
    </h1>
    <h2>Filtrar Resultados</h2>
    <form method="GET" action="incidencias.php">
        <table id="filter-table">
            <thead>
                <tr>
                    <th>Incidencia</th>
                    <th>Usuario</th>
                    <th>Restricción</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="filter-incidencia" placeholder="Incidencia" value="<?php echo htmlspecialchars($filters['incidencia']); ?>"></td>
                    <td><input type="text" name="filter-usuario" placeholder="Usuario" value="<?php echo htmlspecialchars($filters['usuario']); ?>"></td>
                    <td>
                        <select name="filter-restriccion">
                            <option value="">Seleccionar Restricción</option>
                            <?php foreach ($restriccion_options as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['restriccion'] == $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="filter-fecha" placeholder="Fecha" value="<?php echo htmlspecialchars($filters['fecha']); ?>"></td>
                </tr>
            </tbody>
        </table>
        <h3>Filtrar por Rango de Fechas</h3>
        <label for="fecha_inicio">Desde:</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
        <label for="fecha_fin">Hasta:</label>
        <input type="date" name="fecha_fin" id="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
        <button type="submit">Filtrar</button>
        <button type="button" onclick="window.location.href='incidencias.php'">Limpiar filtros</button>
    </form>
    <table id="data-table">
        <thead>
            <tr>
                <th>Incidencia</th>
                <th>Usuario</th>
                <th>Restricción</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['incidencia']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['restriccion']) . "</td>";
                    echo "<td>" . htmlspecialchars(date('d-m-Y H:i', strtotime($row['fecha']))) . "</td>";
                    echo "<td>
                            <form method='POST' action='borrar_incidencia.php' style='display:inline;'>
                                <input type='hidden' name='incidencia' value='" . htmlspecialchars($row['incidencia']) . "'>
                                <button type='submit'>Borrar incidencia</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay datos disponibles</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>