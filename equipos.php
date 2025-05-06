<?php
// Verificar si el usuario ha iniciado sesión
if (!isset($_COOKIE['usuario'])) {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20primero.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Paginación
$results_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Filtros
$filters = [
    'mac' => isset($_GET['filter-mac']) ? $_GET['filter-mac'] : '',
    'version' => isset($_GET['filter-version']) ? $_GET['filter-version'] : '',
    'admins' => isset($_GET['filter-admins']) ? $_GET['filter-admins'] : '',
    'maquina' => isset($_GET['filter-maquina']) ? $_GET['filter-maquina'] : '',
    'nomusuari' => isset($_GET['filter-nomusuari']) ? $_GET['filter-nomusuari'] : '',
    'connexions' => isset($_GET['filter-connexions']) ? $_GET['filter-connexions'] : '',
    'data_restauracio' => isset($_GET['filter-data_restauracio']) ? $_GET['filter-data_restauracio'] : '',
    'restriccio' => isset($_GET['filter-restriccio']) ? $_GET['filter-restriccio'] : '',
    'serial' => isset($_GET['filter-serial']) ? $_GET['filter-serial'] : '',
    'model' => isset($_GET['filter-model']) ? $_GET['filter-model'] : ''
];

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'mac';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';

// Construir la consulta SQL con paginación, filtros y ordenación
$sql = "SELECT mac, version, admins, maquina, nomusuari, connexions, data_restauracio, restriccio, serial, model FROM equipos WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') {
        $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

$sql .= " ORDER BY $order_by $order_dir LIMIT $start_from, $results_per_page";

// Ejecutar la consulta y verificar errores
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "SELECT COUNT(*) FROM equipos WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') {
        $total_sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
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
    <title>Datos de Equipos</title>
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
        input[type="text"], select {
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
        Datos de Equipos
    </h1>

    <h2>Filtrar Resultados</h2>
    <form method="GET" action="equipos.php">
        <table id="filter-table">
            <thead>
                <tr>
                    <th>MAC</th>
                    <th>Versión</th>
                    <th>Admins</th>
                    <th>Máquina</th>
                    <th>Nombre de Usuario</th>
                    <th>Conexiones</th>
                    <th>Fecha de Restauración</th>
                    <th>Restricción</th>
                    <th>Serial</th>
                    <th>Modelo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="filter-mac" value="<?= htmlspecialchars($filters['mac']) ?>" placeholder="Filtrar MAC"></td>
                    <td><input type="text" name="filter-version" value="<?= htmlspecialchars($filters['version']) ?>" placeholder="Filtrar Versión"></td>
                    <td><input type="text" name="filter-admins" value="<?= htmlspecialchars($filters['admins']) ?>" placeholder="Filtrar Admins"></td>
                    <td><input type="text" name="filter-maquina" value="<?= htmlspecialchars($filters['maquina']) ?>" placeholder="Filtrar Máquina"></td>
                    <td><input type="text" name="filter-nomusuari" value="<?= htmlspecialchars($filters['nomusuari']) ?>" placeholder="Filtrar Nombre de Usuario"></td>
                    <td><input type="text" name="filter-connexions" value="<?= htmlspecialchars($filters['connexions']) ?>" placeholder="Filtrar Conexiones"></td>
                    <td><input type="text" name="filter-data_restauracio" value="<?= htmlspecialchars($filters['data_restauracio']) ?>" placeholder="Filtrar Fecha de Restauración"></td>
                    <td>
                        <select name="filter-restriccio">
                            <option value="">Filtrar Restricción</option>
                            <option value="0" <?= $filters['restriccio'] === "0" ? 'selected' : '' ?>>0</option>
                            <option value="1" <?= $filters['restriccio'] === "1" ? 'selected' : '' ?>>1</option>
                            <option value="2" <?= $filters['restriccio'] === "2" ? 'selected' : '' ?>>2</option>
                        </select>
                    </td>
                    <td><input type="text" name="filter-serial" value="<?= htmlspecialchars($filters['serial']) ?>" placeholder="Filtrar Serial"></td>
                    <td><input type="text" name="filter-model" value="<?= htmlspecialchars($filters['model']) ?>" placeholder="Filtrar Modelo"></td>
                </tr>
            </tbody>
        </table>
        <button type="submit">Filtrar</button>
        <button type="button" onclick="window.location.href='equipos.php'">Limpiar filtros</button>
    </form>

    <table id="data-table">
        <thead>
            <tr>
                <th>MAC<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'mac', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'mac', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Versión<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'version', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'version', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Admins<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'admins', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'admins', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Máquina<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'maquina', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'maquina', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Nombre de Usuario<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nomusuari', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nomusuari', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Conexiones<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'connexions', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'connexions', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Fecha de Restauración<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'data_restauracio', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'data_restauracio', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Restricción<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Serial<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'serial', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'serial', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Modelo<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'model', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'model', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        if ($key === 'data_restauracio') {
                            $formatted_date = date('d-m-Y', strtotime($value));
                            echo "<td>" . htmlspecialchars($formatted_date) . "</td>";
                        } else {
                            echo "<td>" . htmlspecialchars($value) . "</td>";
                        }
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No hay datos disponibles</td></tr>";
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