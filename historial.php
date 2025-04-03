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

// Filtros
$filters = [
    'mac' => isset($_GET['filter-mac']) ? $_GET['filter-mac'] : '',
    'nomusuari' => isset($_GET['filter-nomusuari']) ? $_GET['filter-nomusuari'] : '',
    'nom' => isset($_GET['filter-nom']) ? $_GET['filter-nom'] : '',
    'cognoms' => isset($_GET['filter-cognoms']) ? $_GET['filter-cognoms'] : '',
    'curs' => isset($_GET['filter-curs']) ? $_GET['filter-curs'] : '',
    'restriccio_equipo' => isset($_GET['filter-restriccio_equipo']) ? $_GET['filter-restriccio_equipo'] : '',
    'restriccio_usuario' => isset($_GET['filter-restriccio_usuario']) ? $_GET['filter-restriccio_usuario'] : '',
    'aula' => isset($_GET['filter-aula']) ? $_GET['filter-aula'] : '',
    'fecha_conexion' => isset($_GET['filter-fecha_conexion']) ? $_GET['filter-fecha_conexion'] : ''
];

// Obtener opciones únicas para los desplegables
$cursos = [];
$restriccion_equipos = [];
$restriccion_usuarios = [];
$clases_antena = [];

// Obtener opciones únicas de "Curso"
$result_cursos = $conn->query("SELECT DISTINCT curs FROM usuaris WHERE curs IS NOT NULL AND curs != ''");
if ($result_cursos) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row['curs'];
    }
}

// Obtener opciones únicas de "Restricción del Equipo"
$result_restriccion_equipos = $conn->query("SELECT DISTINCT restriccio FROM historial WHERE restriccio IS NOT NULL");
if ($result_restriccion_equipos) {
    while ($row = $result_restriccion_equipos->fetch_assoc()) {
        $restriccion_equipos[] = $row['restriccio'];
    }
}

// Obtener opciones únicas de "Restricción del Usuario"
$result_restriccion_usuarios = $conn->query("SELECT DISTINCT restriccio FROM usuaris WHERE restriccio IS NOT NULL");
if ($result_restriccion_usuarios) {
    while ($row = $result_restriccion_usuarios->fetch_assoc()) {
        $restriccion_usuarios[] = $row['restriccio'];
    }
}

// Obtener opciones únicas de "Clase de la Antena"
$result_clases_antena = $conn->query("SELECT DISTINCT aula FROM antenas WHERE aula IS NOT NULL AND aula != ''");
if ($result_clases_antena) {
    while ($row = $result_clases_antena->fetch_assoc()) {
        $clases_antena[] = $row['aula'];
    }
}

// Paginación
$results_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Construir la consulta SQL con las uniones necesarias
$sql = "
    SELECT 
        h.mac,
        h.nomusuari,
        u.nom,
        u.cognoms,
        u.curs,
        h.restriccio AS restriccio_equipo,
        u.restriccio AS restriccio_usuario,
        UPPER(a.aula) AS clase_antena,
        h.fecha_conexion
    FROM historial h
    LEFT JOIN usuaris u ON h.nomusuari = u.nomusuari
    LEFT JOIN antenas a ON UPPER(h.macssid) = a.mac
    WHERE 1=1
";

// Aplicar filtros
foreach ($filters as $key => $value) {
    if (!empty($value)) {
        if ($key === 'fecha_conexion') {
            $sql .= " AND h.fecha_conexion LIKE '%" . $conn->real_escape_string($value) . "%'";
        } elseif ($key === 'restriccio_equipo') {
            $sql .= " AND h.restriccio = " . (int)$value;
        } elseif ($key === 'restriccio_usuario') {
            $sql .= " AND u.restriccio = " . (int)$value;
        } elseif ($key === 'aula') {
            $sql .= " AND UPPER(a.aula) LIKE '%" . $conn->real_escape_string($value) . "%'";
        } else {
            $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
        }
    }
}

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'h.fecha_conexion';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';
$sql .= " ORDER BY $order_by $order_dir LIMIT $start_from, $results_per_page";

// Ejecutar la consulta
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "
    SELECT COUNT(*) 
    FROM historial h
    LEFT JOIN usuaris u ON h.nomusuari = u.nomusuari
    LEFT JOIN antenas a ON UPPER(h.macssid) = a.mac
    WHERE 1=1
";

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
        <table>
            <thead>
                <tr>
                    <th>MAC</th>
                    <th>Nombre de Usuario</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Curso</th>
                    <th>Restricción del Equipo</th>
                    <th>Restricción del Usuario</th>
                    <th>Clase de la Antena</th>
                    <th>Fecha de Conexión</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="filter-mac" value="<?php echo htmlspecialchars($filters['mac']); ?>"></td>
                    <td><input type="text" name="filter-nomusuari" value="<?php echo htmlspecialchars($filters['nomusuari']); ?>"></td>
                    <td><input type="text" name="filter-nom" value="<?php echo htmlspecialchars($filters['nom']); ?>"></td>
                    <td><input type="text" name="filter-cognoms" value="<?php echo htmlspecialchars($filters['cognoms']); ?>"></td>
                    <td>
                        <select name="filter-curs">
                            <option value="">Curso</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo htmlspecialchars($curso); ?>" <?php echo $filters['curs'] == $curso ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="filter-restriccio_equipo">
                            <option value="">Restricción</option>
                            <?php foreach ($restriccion_equipos as $restriccion): ?>
                                <option value="<?php echo htmlspecialchars($restriccion); ?>" <?php echo $filters['restriccio_equipo'] == $restriccion ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($restriccion); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="filter-restriccio_usuario">
                            <option value="">Restricción</option>
                            <?php foreach ($restriccion_usuarios as $restriccion): ?>
                                <option value="<?php echo htmlspecialchars($restriccion); ?>" <?php echo $filters['restriccio_usuario'] == $restriccion ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($restriccion); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="filter-aula">
                            <option value="">Clase</option>
                            <?php foreach ($clases_antena as $aula): ?>
                                <option value="<?php echo htmlspecialchars($aula); ?>" <?php echo $filters['aula'] == $aula ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($aula); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="filter-fecha_conexion" value="<?php echo htmlspecialchars($filters['fecha_conexion']); ?>"></td>
                </tr>
            </tbody>
        </table>
        <button type="submit">Filtrar</button>
        <button type="button" onclick="window.location.href='historial.php'">Limpiar filtros</button>
    </form>
    <table>
    <thead>
    <tr>
        <th>MAC<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'mac', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'mac', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Nombre de Usuario<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nomusuari', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nomusuari', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Nombre<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nom', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nom', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Apellidos<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'cognoms', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'cognoms', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Curso<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'curs', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'curs', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Restricción del Equipo<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio_equipo', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio_equipo', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Restricción del Usuario<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio_usuario', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio_usuario', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Clase de la Antena<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'clase_antena', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'clase_antena', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
        <th>Fecha de Conexión<br>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'fecha_conexion', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'fecha_conexion', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
        </th>
    </tr>
</thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['mac']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nomusuari']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['cognoms']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['curs']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['restriccio_equipo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['restriccio_usuario']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['clase_antena']) . "</td>";
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
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?page=$i'>$i</a> ";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>