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

// Obtener valores únicos de las columnas 'restriccion_equipo' y 'restriccion_usuario'
$restriccion_equipo_sql = "SELECT DISTINCT restriccion_equipo FROM incidencias";
$restriccion_equipo_result = $conn->query($restriccion_equipo_sql);
$restriccion_equipo_options = [];
if ($restriccion_equipo_result->num_rows > 0) {
    while ($row = $restriccion_equipo_result->fetch_assoc()) {
        $restriccion_equipo_options[] = $row['restriccion_equipo'];
    }
}

$restriccion_usuario_sql = "SELECT DISTINCT restriccion_usuario FROM incidencias";
$restriccion_usuario_result = $conn->query($restriccion_usuario_sql);
$restriccion_usuario_options = [];
if ($restriccion_usuario_result->num_rows > 0) {
    while ($row = $restriccion_usuario_result->fetch_assoc()) {
        $restriccion_usuario_options[] = $row['restriccion_usuario'];
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
    'equipo' => isset($_GET['filter-equipo']) ? $_GET['filter-equipo'] : '',
    'clase' => isset($_GET['filter-clase']) ? $_GET['filter-clase'] : '',
    'restriccion_equipo' => isset($_GET['filter-restriccion_equipo']) ? $_GET['filter-restriccion_equipo'] : '',
    'restriccion_usuario' => isset($_GET['filter-restriccion_usuario']) ? $_GET['filter-restriccion_usuario'] : '',
    'fecha' => isset($_GET['filter-fecha']) ? $_GET['filter-fecha'] : ''
];

// Filtros de fecha
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'fecha'; // Ordenar por fecha por defecto
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc'; // Orden ascendente por defecto

// Construir la consulta SQL con paginación, filtros y ordenación
$sql = "SELECT id, incidencia, usuario, equipo, clase, restriccion_equipo, restriccion_usuario, fecha FROM incidencias WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') {
        $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_fin_adjusted = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
    $sql .= " AND fecha BETWEEN '" . $conn->real_escape_string($fecha_inicio) . "' AND '" . $conn->real_escape_string($fecha_fin_adjusted) . "'";
}

// Ordenar por fecha de más antigua a más nueva
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
    <script>
        function confirmarBorrado(form) {
            const confirmacion = confirm("¿Estás seguro de que deseas borrar esta incidencia?");
            if (confirmacion) {
                form.submit();
            }
            return false;
        }
    </script>
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
                    <th>Equipo</th>
                    <th>Clase</th>
                    <th>Restricción Equipo</th>
                    <th>Restricción Usuario</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="filter-incidencia" placeholder="Incidencia" value="<?php echo htmlspecialchars($filters['incidencia']); ?>"></td>
                    <td><input type="text" name="filter-usuario" placeholder="Usuario" value="<?php echo htmlspecialchars($filters['usuario']); ?>"></td>
                    <td><input type="text" name="filter-equipo" placeholder="Equipo" value="<?php echo htmlspecialchars($filters['equipo']); ?>"></td>
                    <td><input type="text" name="filter-clase" placeholder="Clase" value="<?php echo htmlspecialchars($filters['clase']); ?>"></td>
                    <td>
                        <select name="filter-restriccion_equipo">
                            <option value="">Seleccionar Restricción Equipo</option>
                            <?php foreach ($restriccion_equipo_options as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['restriccion_equipo'] == $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="filter-restriccion_usuario">
                            <option value="">Seleccionar Restricción Usuario</option>
                            <?php foreach ($restriccion_usuario_options as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['restriccion_usuario'] == $option ? 'selected' : ''; ?>>
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
                <th>Equipo</th>
                <th>Clase</th>
                <th>Restricción Equipo</th>
                <th>Restricción Usuario</th>
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
                    echo "<td>" . htmlspecialchars($row['equipo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['clase']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['restriccion_equipo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['restriccion_usuario']) . "</td>";
                    echo "<td>" . htmlspecialchars(date('d-m-Y H:i', strtotime($row['fecha']))) . "</td>";
                    echo "<td>
                            <form method='POST' action='borrar_incidencia.php' style='display:inline;' onsubmit='return confirmarBorrado(this);'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                <button type='submit'>Borrar incidencia</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No hay datos disponibles</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            $query_params = array_merge($_GET, ['page' => $i]);
            echo "<a href='?" . http_build_query($query_params) . "'>" . $i . "</a> ";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>