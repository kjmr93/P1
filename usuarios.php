<?php
// Verificar si el usuario ha iniciado sesión
if (!isset($_COOKIE['usuario'])) {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20primero.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Obtener valores únicos de la columna 'curs'
$curs_sql = "SELECT DISTINCT curs FROM usuaris ORDER BY curs ASC";
$curs_result = $conn->query($curs_sql);
$curs_options = [];
if ($curs_result->num_rows > 0) {
    while ($row = $curs_result->fetch_assoc()) {
        $curs_options[] = $row['curs'];
    }
}

// Obtener valores únicos de la columna 'clase'
$clase_sql = "SELECT DISTINCT clase FROM usuaris ORDER BY clase ASC";
$clase_result = $conn->query($clase_sql);
$clase_options = [];
if ($clase_result->num_rows > 0) {
    while ($row = $clase_result->fetch_assoc()) {
        $clase_options[] = $row['clase'];
    }
}

// Obtener valores únicos de la columna 'restriccio'
$restriccio_sql = "SELECT DISTINCT restriccio FROM usuaris";
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
    'nomusuari' => isset($_GET['filter-nomusuari']) ? $_GET['filter-nomusuari'] : '',
    'nom' => isset($_GET['filter-nom']) ? $_GET['filter-nom'] : '',
    'cognoms' => isset($_GET['filter-cognoms']) ? $_GET['filter-cognoms'] : '',
    'cognoms2' => isset($_GET['filter-cognoms2']) ? $_GET['filter-cognoms2'] : '',
    'curs' => isset($_GET['filter-curs']) ? $_GET['filter-curs'] : '',
    'clase' => isset($_GET['filter-clase']) ? $_GET['filter-clase'] : '',
    'restriccio' => isset($_GET['filter-restriccio']) ? $_GET['filter-restriccio'] : ''
];

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'cognoms';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';

// Construir la consulta SQL con paginación, filtros y ordenación
$sql = "SELECT * FROM usuaris WHERE 1=1";

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
$total_sql = "SELECT COUNT(*) FROM usuaris WHERE 1=1";

foreach ($filters as $key => $value) {
    if (isset($value) && $value !== '') {
        $total_sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

$total_result = $conn->query($total_sql);
if (!$total_result) {
    die("Error en la consulta de conteo: " . $conn->error);
}

$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $results_per_page);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Usuarios</title>
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
            margin: 20px auto;
            padding: 10px 0;
        }
        .pagination a {
            color: #4a90e2;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            display: inline-block;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.active {
            background-color: #4a90e2;
            color: white;
            border: 1px solid #4a90e2;
        }

        .pagination span {
            padding: 8px 16px;
            color: #999;
            margin: 0 4px;
            display: inline-block;
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
        .restriccion-form {
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 0;
            padding: 0;
        }
        .restriccion-form select {
            width: auto;
            padding: 2px 5px;
            font-size: 14px;
            height: auto;
        }
        .restriccion-form button {
            padding: 2px 8px;
            font-size: 14px;
            height: auto;
        }
        td {
            vertical-align: middle;
        }
        #toggle-opciones {
            margin-bottom: 20px;
            margin-left: 20px;
        }
    </style>
</head>
<body>
<h1>
        <a href="inicio.php" class="inicio-button">Inicio</a>
        Datos de Usuarios
    </h1>

    <h2>Filtrar Resultados</h2><br>
    <button id="toggle-opciones">
        Opciones
    </button>

    <div id="opciones" style="display: none; margin-bottom: 20px;">
        <form method="POST" action="importar_usuarios.php" enctype="multipart/form-data" style="margin-bottom: 20px;">
            <label for="archivo-usuarios" style="font-weight: bold;">Importar usuarios:</label>
            <input type="file" name="archivo_usuarios" id="archivo-usuarios" accept=".xls" required>
            <button type="submit" style="background-color: #4a90e2; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                Subir archivo
            </button>
        </form>
        <form method="POST" action="actualizar_usuarios.php" enctype="multipart/form-data" style="margin-bottom: 20px;">
            <label for="archivo-actualizar" style="font-weight: bold;">Actualizar usuarios:</label>
            <input type="file" name="archivo_usuarios" id="archivo-actualizar" accept=".xls" required>
            <button type="submit" style="background-color: #4a90e2; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                Actualizar archivo
            </button>
        </form>
    </div>

    <script>
        document.getElementById('toggle-opciones').addEventListener('click', function () {
            const opcionesDiv = document.getElementById('opciones');
            if (opcionesDiv.style.display === 'none') {
                opcionesDiv.style.display = 'block';
            } else {
                opcionesDiv.style.display = 'none';
            }
        });
    </script>
    <form method="GET" action="usuarios.php">
        <table id="filter-table">
            <thead>
                <tr>
                    <th>Nombre de Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido 1</th>
                    <th>Apellido 2</th>
                    <th>Curso</th>
                    <th>Clase</th>
                    <th>Restricción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="filter-nomusuari" placeholder="Filtrar Nombre de Usuario" value="<?php echo htmlspecialchars($filters['nomusuari']); ?>"></td>
                    <td><input type="text" name="filter-nom" placeholder="Filtrar Nombre" value="<?php echo htmlspecialchars($filters['nom']); ?>"></td>
                    <td><input type="text" name="filter-cognoms" placeholder="Filtrar Apellidos" value="<?php echo htmlspecialchars($filters['cognoms']); ?>"></td>
                    <td><input type="text" name="filter-cognoms2" placeholder="Filtrar Apellidos" value="<?php echo htmlspecialchars($filters['cognoms2']); ?>"></td>
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
                    <td>
                        <select name="filter-clase">
                            <option value="">Filtrar Clase</option>
                            <?php foreach ($clase_options as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['clase'] == $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
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
                </tr>
            </tbody>
        </table>
        <button type="submit">Filtrar</button>
        <button type="button" onclick="window.location.href='usuarios.php'">Limpiar filtros</button>
    </form>

    <table id="data-table">
        <thead>
            <tr>
                <th>Nombre de Usuario<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nomusuari', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nomusuari', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Nombre<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nom', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'nom', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Apellido 1<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'cognoms', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'cognoms', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Apellido 2<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'cognoms2', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'cognoms2', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Curso<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'curs', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'curs', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Clase<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'clase', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'clase', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Restricción<br>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'restriccio', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
                </th>
                <th>Modificar Restricción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nomusuari']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['cognoms']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['cognoms2']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['curs']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['clase']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['restriccio']) . "</td>";
                    echo "<td>
                            <form method='POST' action='modificar_restriccion.php' class='restriccion-form'>
                                <input type='hidden' name='nomusuari' value='" . htmlspecialchars($row['nomusuari']) . "'>
                                <select name='nueva_restriccion'>
                                    <option value='0'>0</option>
                                    <option value='1'>1</option>
                                    <option value='2'>2</option>
                                </select>
                                <button type='submit'>Confirmar</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay datos disponibles</td></tr>";
            }
            ?>
        </tbody>
    </table>
   
    <div class="pagination">
            <?php
            if ($total_pages > 1) {
                // Mostrar las 3 primeras páginas
                for ($i = 1; $i <= 3 && $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo "<a href='?" . http_build_query(array_merge($_GET, ['page' => $i])) . "' class='active'>$i</a> ";
                    } else {
                        echo "<a href='?" . http_build_query(array_merge($_GET, ['page' => $i])) . "'>$i</a> ";
                    }
                }

                // Mostrar puntos suspensivos si la página actual está lejos de las primeras páginas
                if ($page > 5) {
                    echo "<span>...</span> ";
                }

                // Mostrar la página inmediatamente anterior, la actual y la siguiente
                for ($i = max(4, $page - 1); $i <= min($total_pages - 3, $page + 1); $i++) {
                    if ($i == $page) {
                        echo "<a href='?" . http_build_query(array_merge($_GET, ['page' => $i])) . "' class='active'>$i</a> ";
                    } else {
                        echo "<a href='?" . http_build_query(array_merge($_GET, ['page' => $i])) . "'>$i</a> ";
                    }
                }

                // Mostrar puntos suspensivos si la página actual está lejos de las últimas páginas
                if ($page < $total_pages - 4) {
                    echo "<span>...</span> ";
                }

                // Mostrar las 3 últimas páginas
                for ($i = max($total_pages - 2, 4); $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo "<a href='?" . http_build_query(array_merge($_GET, ['page' => $i])) . "' class='active'>$i</a> ";
                    } else {
                        echo "<a href='?" . http_build_query(array_merge($_GET, ['page' => $i])) . "'>$i</a> ";
                    }
                }
            }
            ?>
        </div>
</body>
</html>

<?php
$conn->close();
?>