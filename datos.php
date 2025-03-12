<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener filtros de la petición prueba de cambio
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

// Construir la consulta SQL con filtros
$sql = "SELECT * FROM datos WHERE 1=1";
foreach ($filters as $key => $value) {
    if (!empty($value)) {
        $sql .= " AND $key LIKE '%" . $conn->real_escape_string($value) . "%'";
    }
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Equipos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Datos de Equipos</h1>
    <form method="GET" action="index.php">
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
                <tr>
                    <th><input type="text" name="filter-mac" value="<?php echo htmlspecialchars($filters['mac']); ?>" placeholder="Filtrar MAC"></th>
                    <th><input type="text" name="filter-version" value="<?php echo htmlspecialchars($filters['version']); ?>" placeholder="Filtrar Versión"></th>
                    <th><input type="text" name="filter-admins" value="<?php echo htmlspecialchars($filters['admins']); ?>" placeholder="Filtrar Admins"></th>
                    <th><input type="text" name="filter-maquina" value="<?php echo htmlspecialchars($filters['maquina']); ?>" placeholder="Filtrar Máquina"></th>
                    <th><input type="text" name="filter-nomusuari" value="<?php echo htmlspecialchars($filters['nomusuari']); ?>" placeholder="Filtrar Nombre de Usuario"></th>
                    <th><input type="text" name="filter-connexions" value="<?php echo htmlspecialchars($filters['connexions']); ?>" placeholder="Filtrar Conexiones"></th>
                    <th><input type="text" name="filter-data_restauracio" value="<?php echo htmlspecialchars($filters['data_restauracio']); ?>" placeholder="Filtrar Fecha de Restauración"></th>
                    <th><input type="text" name="filter-restriccio" value="<?php echo htmlspecialchars($filters['restriccio']); ?>" placeholder="Filtrar Restricción"></th>
                    <th><input type="text" name="filter-snap_installat" value="<?php echo htmlspecialchars($filters['snap_installat']); ?>" placeholder="Filtrar Snap Instalado"></th>
                    <th><input type="text" name="filter-snap_vpns" value="<?php echo htmlspecialchars($filters['snap_vpns']); ?>" placeholder="Filtrar Snap VPNs"></th>
                    <th><input type="text" name="filter-snap_opera" value="<?php echo htmlspecialchars($filters['snap_opera']); ?>" placeholder="Filtrar Snap Opera"></th>
                    <th><input type="text" name="filter-windows" value="<?php echo htmlspecialchars($filters['windows']); ?>" placeholder="Filtrar Windows"></th>
                    <th><input type="text" name="filter-serial" value="<?php echo htmlspecialchars($filters['serial']); ?>" placeholder="Filtrar Serial"></th>
                    <th><input type="text" name="filter-model" value="<?php echo htmlspecialchars($filters['model']); ?>" placeholder="Filtrar Modelo"></th>
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
                $conn->close();
                ?>
            </tbody>
        </table>
        <button type="submit">Filtrar</button>
    </form>
</body>
</html>