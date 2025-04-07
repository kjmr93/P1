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
$macssid = $_POST['macssid'];


$fecha_conexion = date('Y-m-d H:i:s');

// Extraer el texto entre los dos puntos y la primera coma del valor obtenido en "connexions"
$nomusuari = '';
if (strpos($connexions, ':') !== false && strpos($connexions, ',') !== false) {
    $start = strpos($connexions, ':') + 1;
    $end = strpos($connexions, ',');
    $nomusuari = substr($connexions, $start, $end - $start);
}

// Obtener el valor de "restriccio" de la tabla "usuaris" basado en "nomusuari"
$restriccio_usuari = 0;
$sql_restriccio = "SELECT restriccio FROM usuaris WHERE nomusuari = ?";
$stmt = $conn->prepare($sql_restriccio);
$stmt->bind_param("s", $nomusuari);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $restriccio_usuari = $row['restriccio'];
}

$stmt->close();

// Insertar datos en la tabla "historial"
$sql = "INSERT INTO historial (mac, version, admins, maquina, nomusuari, connexions, data_restauracio, restriccio, snap_installat, snap_vpns, snap_opera, windows, serial, model, macssid, restriccio_usuari, fecha_conexion)
VALUES ('$mac', '$version', '$admins', '$maquina', '$nomusuari', '$connexions', '$data_restauracio', '$restriccio', '$snap_installat', '$snap_vpns', '$snap_opera', '$windows', '$serial', '$model', '$macssid', '$restriccio_usuari', '$fecha_conexion')";

if ($conn->query($sql) === TRUE) {
    echo "Datos insertados correctamente en la tabla historial";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

header("Location: historial.php");
exit();
?>