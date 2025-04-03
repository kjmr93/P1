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

// Insertar datos en la tabla "historial"
$sql = "INSERT INTO historial (mac, version, admins, maquina, nomusuari, connexions, data_restauracio, restriccio, snap_installat, snap_vpns, snap_opera, windows, serial, model, macssid, restriccio_usuari)
VALUES ('$mac', '$version', '$admins', '$maquina', '$nomusuari', '$connexions', '$data_restauracio', '$restriccio', '$snap_installat', '$snap_vpns', '$snap_opera', '$windows', '$serial', '$model', '$macssid', '$restriccio_usuari')";

if ($conn->query($sql) === TRUE) {
    echo "Datos insertados correctamente en la tabla historial";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

header("Location: historial.php");
exit();
?>