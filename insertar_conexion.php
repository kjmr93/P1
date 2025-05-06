<?php
// Conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pruebas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Datos petición
$mac = $_POST['mac'];
$version = $_POST['version'];
$admins = $_POST['admins'];
$maquina = $_POST['maquina'];
$connexions = $_POST['connexions'];
$data_restauracio = $_POST['data_restauracio'];
$restriccio = $_POST['restriccio'];
$snap_installat = $_POST['snap_installat'];
$snap_vpns = $_POST['snap_vpns'];
$snap_opera = $_POST['snap_opera'];
$windows = $_POST['windows'];
$serial = $_POST['serial'];
$model = $_POST['model'];
$macssid = $_POST['macssid']; // Nueva línea para capturar el valor de macssid

// Obtener la fecha y hora actuales del sistema
$fecha_conexion = date('Y-m-d H:i:s'); // Formato de fecha y hora: YYYY-MM-DD HH:MM:SS

// Extraer el texto entre los dos puntos y la primera coma de "connexions"
$nomusuari = '';
if (strpos($connexions, ':') !== false && strpos($connexions, ',') !== false) {
    $start = strpos($connexions, ':') + 1; // Posición después de los dos puntos
    $end = strpos($connexions, ','); // Posición de la primera coma
    $nomusuari = substr($connexions, $start, $end - $start); // Extraer el texto
}

// Obtener el valor de "restriccio" de la tabla "usuaris" basado en "nomusuari"
$restriccio_usuari = 0; // Valor predeterminado en caso de que no se encuentre el usuario
$sql_restriccio = "SELECT restriccio FROM usuaris WHERE nomusuari = ?";
$stmt = $conn->prepare($sql_restriccio);
$stmt->bind_param("s", $nomusuari);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $restriccio_usuari = $row['restriccio']; // Asignar el valor de "restriccio" si se encuentra
}

$stmt->close();

// Comprobar si "mac" existe en la tabla "equipos"
$sql_check_mac = "SELECT mac FROM equipos WHERE mac = ?";
$stmt_check = $conn->prepare($sql_check_mac);
$stmt_check->bind_param("s", $mac);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    // Si "mac" no existe, insertar los datos en la tabla "equipos"
    $sql_insert_equipos = "INSERT INTO equipos (mac, version, admins, maquina, nomusuari, connexions, data_restauracio, restriccio, serial, model)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_equipos = $conn->prepare($sql_insert_equipos);
    $stmt_insert_equipos->bind_param(
        "ssssssssss",
        $mac,
        $version,
        $admins,
        $maquina,
        $nomusuari,
        $connexions,
        $data_restauracio,
        $restriccio,
        $serial,
        $model
    );
    $stmt_insert_equipos->execute();
    $stmt_insert_equipos->close();
}

$stmt_check->close();

// Comprobar si "mac" ya existe en la tabla "historial"
$sql_check_historial = "SELECT mac, macssid, fecha_conexion FROM historial WHERE mac = ? ORDER BY fecha_conexion DESC LIMIT 1";
$stmt_check_historial = $conn->prepare($sql_check_historial);
$stmt_check_historial->bind_param("s", $mac);
$stmt_check_historial->execute();
$result_check_historial = $stmt_check_historial->get_result();

$insertar_datos = true; // Bandera para determinar si se deben insertar los datos

if ($result_check_historial->num_rows > 0) {
    // Si "mac" ya existe, obtener el valor más reciente de "macssid" y "fecha_conexion"
    $row_historial = $result_check_historial->fetch_assoc();
    $existing_macssid = $row_historial['macssid'];
    $existing_fecha_conexion = $row_historial['fecha_conexion'];

    // Comparar "macssid" y las fechas
    if ($existing_macssid === $macssid) {
        // Si "macssid" es igual, comparar las fechas (sin incluir la hora)
        $existing_date = date('Y-m-d', strtotime($existing_fecha_conexion));
        $new_date = date('Y-m-d', strtotime($fecha_conexion));

        if ($existing_date === $new_date) {
            // Si la fecha es la misma, no insertar los datos
            $insertar_datos = false;
        }
    }
    // Si "macssid" es distinto o la fecha es diferente, se insertarán los datos
}

$stmt_check_historial->close();

if ($insertar_datos) {
    // Insertar datos en la tabla "historial"
    $sql = "INSERT INTO historial (mac, version, admins, maquina, nomusuari, connexions, data_restauracio, restriccio, snap_installat, snap_vpns, snap_opera, windows, serial, model, macssid, restriccio_usuari, fecha_conexion)
    VALUES ('$mac', '$version', '$admins', '$maquina', '$nomusuari', '$connexions', '$data_restauracio', '$restriccio', '$snap_installat', '$snap_vpns', '$snap_opera', '$windows', '$serial', '$model', '$macssid', '$restriccio_usuari', '$fecha_conexion')";

    if ($conn->query($sql) === TRUE) {
        echo "Datos insertados correctamente en la tabla historial";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "No se insertaron los datos porque ya existe un registro con la misma MAC, MACSSID y fecha.";
}

$conn->close();

exit();
?>