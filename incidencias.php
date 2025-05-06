<?php
// Verificar si el usuario ha iniciado sesión
if (!isset($_COOKIE['usuario'])) {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20primero.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Obtener valores únicos para el filtro de clase desde la tabla antenas columna aula
$clase_options = [];
$clase_sql = "SELECT DISTINCT aula FROM antenas ORDER BY aula ASC"; // Ordenar alfabéticamente
$clase_result = $conn->query($clase_sql);
if ($clase_result->num_rows > 0) {
    while ($row = $clase_result->fetch_assoc()) {
        $clase_options[] = $row['aula'];
    }
}

// Filtros
$filters = [
    'incidencia' => isset($_GET['filter-incidencia']) ? $_GET['filter-incidencia'] : '',
    'usuario' => isset($_GET['filter-usuario']) ? $_GET['filter-usuario'] : '',
    'admins' => isset($_GET['filter-admins']) ? $_GET['filter-admins'] : '',
    'equipo' => isset($_GET['filter-equipo']) ? $_GET['filter-equipo'] : '',
    'clase' => isset($_GET['filter-clase']) ? $_GET['filter-clase'] : '',
    'snap_installat' => isset($_GET['filter-snap_installat']) ? $_GET['filter-snap_installat'] : '',
    'snap_vpns' => isset($_GET['filter-snap_vpns']) ? $_GET['filter-snap_vpns'] : '',
    'snap_opera' => isset($_GET['filter-snap_opera']) ? $_GET['filter-snap_opera'] : '',
    'windows' => isset($_GET['filter-windows']) ? $_GET['filter-windows'] : '',
    'restriccion_equipo' => isset($_GET['filter-restriccion_equipo']) ? $_GET['filter-restriccion_equipo'] : '',
    'restriccion_usuario' => isset($_GET['filter-restriccion_usuario']) ? $_GET['filter-restriccion_usuario'] : '',
    'fecha' => isset($_GET['filter-fecha']) ? $_GET['filter-fecha'] : ''
];

// Mostrar solucionados
$incluir_solucionados = isset($_GET['incluir_solucionados']) && $_GET['incluir_solucionados'] === '1';

// Paginación
$results_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Construir la consulta SQL con filtros
$sql = "SELECT id, incidencia, usuario, admins, equipo, clase, snap_installat, snap_vpns, snap_opera, windows, restriccion_equipo, restriccion_usuario, fecha, estado FROM incidencias WHERE 1=1";

// Aplicar filtros
foreach ($filters as $key => $value) {
    if ($value !== '') { 
        $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

// Filtrar por rango de fechas
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND fecha BETWEEN '" . $conn->real_escape_string($fecha_inicio) . " 00:00:00' AND '" . $conn->real_escape_string($fecha_fin) . " 23:59:59'";
}

// Filtrar por estado (pendiente por defecto)
if (!$incluir_solucionados) {
    $sql .= " AND estado = 0";
}

// Ordenar por fecha
$sql .= " ORDER BY fecha ASC LIMIT $start_from, $results_per_page";

// Ejecutar la consulta
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "SELECT COUNT(*) FROM incidencias WHERE 1=1";

foreach ($filters as $key => $value) {
    if (!empty($value)) {
        $total_sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
     }
    }

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $total_sql .= " AND fecha BETWEEN '" . $conn->real_escape_string($fecha_inicio) . "' AND '" . $conn->real_escape_string($fecha_fin) . "'";
    }

    if (!$incluir_solucionados) {
        $total_sql .= " AND estado = 0";
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
        #data-table th:nth-child(1),
        #data-table td:nth-child(1) {
            width: 400px;
        }
        #data-table th:nth-child(10),
        #data-table td:nth-child(10),
        #data-table th:nth-child(11),
        #data-table td:nth-child(11) {
            width: 120px;
        }
        #data-table th:nth-child(12),
        #data-table td:nth-child(12) {
            width: 350px;
            white-space: pre-line;
        }       
        #data-table td form button {
            margin-top: 5px;
        }
    </style>
    <script>
        function confirmarSolucion(form) {
            const confirmacion = confirm("¿Estás seguro de que deseas marcar como solucionada esta incidencia?");
            if (confirmacion) {
                form.submit();
            }
            return false;
        }
        function toggleSolucionados() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('incluir_solucionados') === '1') {
                urlParams.delete('incluir_solucionados');
            } else {
                urlParams.set('incluir_solucionados', '1');
            }
            window.location.search = urlParams.toString();
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
                <th>Administradores</th>
                <th>Equipo</th>
                <th>Clase</th>
                <th>Snap Instalado</th>
                <th>Snap VPN</th>
                <th>Snap Opera</th>
                <th>Windows</th>
                <th>Restricción Equipo</th>
                <th>Restricción Usuario</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="text" name="filter-incidencia" placeholder="Incidencia" value="<?php echo htmlspecialchars($filters['incidencia']); ?>"></td>
                <td><input type="text" name="filter-usuario" placeholder="Usuario" value="<?php echo htmlspecialchars($filters['usuario']); ?>"></td>
                <td><input type="text" name="filter-admins" placeholder="Administradores" value="<?php echo htmlspecialchars($filters['admins']); ?>"></td>
                <td><input type="text" name="filter-equipo" placeholder="Equipo" value="<?php echo htmlspecialchars($filters['equipo']); ?>"></td>
                <td>
                    <select name="filter-clase">
                        <option value="">Seleccionar Clase</option>
                        <?php foreach ($clase_options as $option): ?>
                            <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['clase'] == $option ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="text" name="filter-snap_installat" placeholder="Snap Instalado" value="<?php echo htmlspecialchars($filters['snap_installat']); ?>"></td>
                <td><input type="text" name="filter-snap_vpns" placeholder="Snap VPN" value="<?php echo htmlspecialchars($filters['snap_vpns']); ?>"></td>
                <td><input type="text" name="filter-snap_opera" placeholder="Snap Opera" value="<?php echo htmlspecialchars($filters['snap_opera']); ?>"></td>
                <td><input type="text" name="filter-windows" placeholder="Windows" value="<?php echo htmlspecialchars($filters['windows']); ?>"></td>
                <td>
                    <select name="filter-restriccion_equipo">
                        <option value="">Seleccionar Restricción Equipo</option>
                        <option value="0" <?php echo $filters['restriccion_equipo'] === "0" ? 'selected' : ''; ?>>0</option>
                        <option value="1" <?php echo $filters['restriccion_equipo'] === "1" ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo $filters['restriccion_equipo'] === "2" ? 'selected' : ''; ?>>2</option>
                    </select>
                </td>
                <td>
                    <select name="filter-restriccion_usuario">
                        <option value="">Seleccionar Restricción Usuario</option>
                        <option value="0" <?php echo $filters['restriccion_usuario'] === "0" ? 'selected' : ''; ?>>0</option>
                        <option value="1" <?php echo $filters['restriccion_usuario'] === "1" ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo $filters['restriccion_usuario'] === "2" ? 'selected' : ''; ?>>2</option>
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
        <button type="button" onclick="toggleSolucionados()">
            <?= $incluir_solucionados ? 'Excluir solucionados' : 'Incluir solucionados' ?>
        </button>
        <button type="submit">Filtrar</button>
        <button type="button" onclick="window.location.href='incidencias.php'">Limpiar filtros</button>
    </form>
    <table id="data-table">
            <thead>
                <tr>
                    <th>Incidencia</th>
                    <th>Usuario</th>
                    <th>Administradores</th>
                    <th>Equipo</th>
                    <th>Clase</th>
                    <th>Snap Instalado</th>
                    <th>Snap VPN</th>
                    <th>Snap Opera</th>
                    <th>Windows</th>
                    <th>Restricción Equipo</th>
                    <th>Restricción Usuario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['incidencia']) ?></td>
                            <td><?= htmlspecialchars($row['usuario']) ?></td>
                            <td><?= htmlspecialchars($row['admins']) ?></td>
                            <td><?= htmlspecialchars($row['equipo']) ?></td>
                            <td><?= htmlspecialchars($row['clase']) ?></td>
                            <td><?= htmlspecialchars($row['snap_installat']) ?></td>
                            <td><?= htmlspecialchars($row['snap_vpns']) ?></td>
                            <td><?= htmlspecialchars($row['snap_opera']) ?></td>
                            <td><?= htmlspecialchars($row['windows']) ?></td>
                            <td><?= htmlspecialchars($row['restriccion_equipo']) ?></td>
                            <td><?= htmlspecialchars($row['restriccion_usuario']) ?></td>
                            <td><?= htmlspecialchars(date('d-m-Y H:i', strtotime($row['fecha']))) ?></td>
                            <td>
                                <?= $row['estado'] == 0 ? 'Pendiente' : 'Solucionado' ?>
                                <?php if ($row['estado'] == 0): ?>
                                    <br>
                                    <form method="POST" action="solucionar_incidencia.php" style="display:inline;" onsubmit="return confirmarSolucion(this);">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button type="submit">Solucionar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="13">No hay datos disponibles</td></tr>
                <?php endif; ?>
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