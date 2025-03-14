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
    'snap_installat' => isset($_GET['filter-snap_installat']) ? $_GET['filter-snap_installat'] : '',
    'snap_vpns' => isset($_GET['filter-snap_vpns']) ? $_GET['filter-snap_vpns'] : '',
    'snap_opera' => isset($_GET['filter-snap_opera']) ? $_GET['filter-snap_opera'] : '',
    'windows' => isset($_GET['filter-windows']) ? $_GET['filter-windows'] : '',
    'serial' => isset($_GET['filter-serial']) ? $_GET['filter-serial'] : '',
    'model' => isset($_GET['filter-model']) ? $_GET['filter-model'] : ''
];

// Construir la consulta SQL con paginación y filtros
$sql = "SELECT * FROM datos WHERE 1=1";

foreach ($filters as $key => $value) {
    if (!empty($value)) {
        $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

$sql .= " LIMIT $start_from, $results_per_page";

// Ejecutar la consulta y verificar errores
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "SELECT COUNT(*) FROM datos WHERE 1=1";

foreach ($filters as $key => $value) {
    if (!empty($value)) {
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
    input[type="text"] {
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
    #filtered-results {
        display: none;
    }
</style>
</head>
<body>
    <h1>Datos de Equipos</h1>
    <table id="data-table">
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
                <th>Snap Instalado</th>
                <th>Snap VPNs</th>
                <th>Snap Opera</th>
                <th>Windows</th>
                <th>Serial</th>
                <th>Modelo</th>
            </tr>
        </thead>
        <tbody>
             <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['mac']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['version']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['admins']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['maquina']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nomusuari']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['connexions']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['data_restauracio']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['restriccio']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['snap_installat']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['snap_vpns']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['snap_opera']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['windows']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['model']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='14'>No hay datos disponibles</td></tr>";
                }
                ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='datos.php?page=" . $i . "'>" . $i . "</a> ";
        }
        ?>
    </div>

    <h2>Filtrar Resultados</h2>
    <form method="GET" action="datos.php">
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
                    <th>Snap Instalado</th>
                    <th>Snap VPNs</th>
                    <th>Snap Opera</th>
                    <th>Windows</th>
                    <th>Serial</th>
                    <th>Modelo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="filter-mac" placeholder="Filtrar MAC"></td>
                    <td><input type="text" name="filter-version" placeholder="Filtrar Versión"></td>
                    <td><input type="text" name="filter-admins" placeholder="Filtrar Admins"></td>
                    <td><input type="text" name="filter-maquina" placeholder="Filtrar Máquina"></td>
                    <td><input type="text" name="filter-nomusuari" placeholder="Filtrar Nombre de Usuario"></td>
                    <td><input type="text" name="filter-connexions" placeholder="Filtrar Conexiones"></td>
                    <td><input type="text" name="filter-data_restauracio" placeholder="Filtrar Fecha de Restauración"></td>
                    <td><input type="text" name="filter-restriccio" placeholder="Filtrar Restricción"></td>
                    <td><input type="text" name="filter-snap_installat" placeholder="Filtrar Snap Instalado"></td>
                    <td><input type="text" name="filter-snap_vpns" placeholder="Filtrar Snap VPNs"></td>
                    <td><input type="text" name="filter-snap_opera" placeholder="Filtrar Snap Opera"></td>
                    <td><input type="text" name="filter-windows" placeholder="Filtrar Windows"></td>
                    <td><input type="text" name="filter-serial" placeholder="Filtrar Serial"></td>
                    <td><input type="text" name="filter-model" placeholder="Filtrar Modelo"></td>
                </tr>
            </tbody>
        </table>
        <button type="submit">Filtrar</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>