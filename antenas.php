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

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'aula';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';

// Construir la consulta SQL con paginación y ordenación
$sql = "SELECT * FROM antenas ORDER BY $order_by $order_dir LIMIT $start_from, $results_per_page";

// Ejecutar la consulta y verificar errores
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "SELECT COUNT(*) FROM antenas";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $results_per_page);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Antenas</title>
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
            width: 40%; /* Ajustar el ancho total de la tabla */
            margin: 20px auto; /* Centrar la tabla */
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            width: 20%; /* Ajustar el ancho de cada columna */
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
    Antenas
</h1>

<table id="data-table">
    <thead>
        <tr>
            <th>Aula<br>
                <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'aula', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'aula', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
            </th>
            <th>MAC<br>
                <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'mac', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'mac', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['aula']) . "</td>";
                echo "<td>" . htmlspecialchars($row['mac']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No hay datos disponibles</td></tr>";
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