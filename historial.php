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

// Obtener valores únicos de la columna 'curs'
$curs_sql = "SELECT DISTINCT curs FROM historial";
$curs_result = $conn->query($curs_sql);
$curs_options = [];
if ($curs_result->num_rows > 0) {
    while ($row = $curs_result->fetch_assoc()) {
        $curs_options[] = $row['curs'];
    }
}

// Obtener valores únicos de la columna 'restriccio'
$restriccio_sql = "SELECT DISTINCT restriccio FROM historial";
$restriccio_result = $conn->query($restriccio_sql);
$restriccio_options = [];
if ($restriccio_result->num_rows > 0) {
    while ($row = $restriccio_result->fetch_assoc()) {
        $restriccio_options[] = $row['restriccio'];
    }
}

// Paginación
$results_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Filtros
$filters = [
    'mac' => isset($_GET['filter-mac']) ? $_GET['filter-mac'] : '',
    'admins' => isset($_GET['filter-admins']) ? $_GET['filter-admins'] : '',
    'nomusuari' => isset($_GET['filter-nomusuari']) ? $_GET['filter-nomusuari'] : '',
    'nom' => isset($_GET['filter-nom']) ? $_GET['filter-nom'] : '',
    'cognoms' => isset($_GET['filter-cognoms']) ? $_GET['filter-cognoms'] : '',
    'curs' => isset($_GET['filter-curs']) ? $_GET['filter-curs'] : '',
    'windows' => isset($_GET['filter-windows']) ? $_GET['filter-windows'] : '',
    'restriccio' => isset($_GET['filter-restriccio']) ? $_GET['filter-restriccio'] : '',
    'fecha_conexion' => isset($_GET['filter-fecha_conexion']) ? $_GET['filter-fecha_conexion'] : ''
];

// Filtros de fecha
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'mac';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';

// Construir la consulta SQL con paginación, filtros y ordenación
$sql = "SELECT * FROM historial WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') { // Cambiado para manejar correctamente el valor "0"
        $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_fin_adjusted = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
    $sql .= " AND fecha_conexion BETWEEN '" . $conn->real_escape_string($fecha_inicio) . "' AND '" . $conn->real_escape_string($fecha_fin_adjusted) . "'";
}

$sql .= " ORDER BY $order_by $order_dir LIMIT $start_from, $results_per_page";

// Ejecutar la consulta y verificar errores
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "SELECT COUNT(*) FROM historial WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') { // Cambiado para manejar correctamente el valor "0"
        $total_sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_fin_adjusted = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
    $total_sql .= " AND fecha_conexion BETWEEN '" . $conn->real_escape_string($fecha_inicio) . "' AND '" . $conn->real_escape_string($fecha_fin_adjusted) . "'";
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
    <title>Historial de Conexiones</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos CSS existentes */
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
            position: relative;
        }
        th a {
            display: inline-block;
            margin: 0 2px;
            color: white;
            text-decoration: none;
            font-size: 12px;
            padding: 2px 5px;
            border-radius: 3px;
        }
        th a.asc {
            background-color: #a8d5e2;
        }
        th a.desc {
            background-color: #e2a8a8;
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
            padding: 8px 16px;
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
        Historial de Conexiones
    </h1>
    <h2>Filtrar Resultados</h2>
    <form method="GET" action="historial.php">
        <table id="filter-table">
            <thead>
                <tr>
                    <th>MAC</th>
                    <th>Admins</th>
                    <th>Nombre de Usuario</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Curso</th>
                    <th>Windows</th>
                    <th>Restricción</th>
                    <th>Fecha de Conexión</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="filter-mac" placeholder="Filtrar MAC" value="<?php echo htmlspecialchars($filters['mac']); ?>"></td>
                    <td><input type="text" name="filter-admins" placeholder="Filtrar Admins" value="<?php echo htmlspecialchars($filters['admins']); ?>"></td>
                    <td><input type="text" name="filter-nomusuari" placeholder="Filtrar Nombre de Usuario" value="<?php echo htmlspecialchars($filters['nomusuari']); ?>"></td>
                    <td><input type="text" name="filter-nom" placeholder="Filtrar Nombre" value="<?php echo htmlspecialchars($filters['nom']); ?>"></td>
                    <td><input type="text" name="filter-cognoms" placeholder="Filtrar Apellidos" value="<?php echo htmlspecialchars($filters['cognoms']); ?>"></td>
                    <td>
                        <select name="filter-curs">
                            <option value="">Filtrar Curso</option>
                            <?php foreach ($curs_options as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['curs'] == $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="filter-windows" placeholder="Filtrar Windows" value="<?php echo htmlspecialchars($filters['windows']); ?>"></td>
                    <td>
                        <select name="filter-restriccio">
                            <option value="">Filtrar Restricción</option>
                            <?php foreach ($restriccio_options as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['restriccio'] == $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="filter-fecha_conexion" placeholder="Filtrar Fecha de Conexión" value="<?php echo htmlspecialchars($filters['fecha_conexion']); ?>"></td>
                </tr>
            </tbody>
        </table>
        <h3>Filtrar por Rango de Fechas</h3>
        <label for="fecha_inicio">Desde:</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
        <label for="fecha_fin">Hasta:</label>
        <input type="date" name="fecha_fin" id="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
        <button type="submit">Filtrar</button>
        <button type="button" onclick="window.location.href='historial.php'">Limpiar filtros</button>
    </form>
    <table id="data-table">
    <thead>
        <tr>
            <?php
            $query_string = $_GET;
            $query_string['order_dir'] = 'asc';
            echo '<th>MAC<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'mac'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'mac', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Admins<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'admins'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'admins', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Nombre de Usuario<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'nomusuari'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'nomusuari', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Nombre<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'nom'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'nom', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Apellidos<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'cognoms'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'cognoms', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Curso<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'curs'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'curs', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Windows<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'windows'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'windows', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Restricción<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'restriccio'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'restriccio', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            echo '<th>Fecha de Conexión<br>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'fecha_conexion'])) . '" class="asc">&#9650;</a>
                <a href="?' . http_build_query(array_merge($query_string, ['order_by' => 'fecha_conexion', 'order_dir' => 'desc'])) . '" class="desc">&#9660;</a>
            </th>';
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['mac']) . "</td>";
                echo "<td>" . htmlspecialchars($row['admins']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nomusuari']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cognoms']) . "</td>";
                echo "<td>" . htmlspecialchars($row['curs']) . "</td>";
                echo "<td>" . htmlspecialchars($row['windows']) . "</td>";
                echo "<td>" . htmlspecialchars($row['restriccio']) . "</td>";
                echo "<td>" . htmlspecialchars(date('d-m-Y H:i', strtotime($row['fecha_conexion']))) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No hay datos disponibles</td></tr>";
        }
        ?>
    </tbody>
</table>

    <div class="pagination">
        <?php
        $query_string = $_GET;
        for ($i = 1; $i <= $total_pages; $i++) {
            $query_string['page'] = $i;
            $query = http_build_query($query_string);
            echo "<a href='historial.php?" . $query . "'>" . $i . "</a> ";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>