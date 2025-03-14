<?php
// Conexión
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Datos petición
$mac = $_POST['mac'];
$version = $_POST['version'];
$admins = $_POST['admins'];
$maquina = $_POST['maquina'];
$nomusuari = $_POST['nomusuari'];
$connexions = $_POST['connexions'];
$data_restauracio = $_POST['data_restauracio'];
$restriccio = $_POST['restriccio'];
$snap_installat = $_POST['snap_installat'];
$snap_vpns = $_POST['snap_vpns'];
$snap_opera = $_POST['snap_opera'];
$windows = $_POST['windows'];
$serial = $_POST['serial'];
$model = $_POST['model'];

// Insertar datos en la base de datos
$sql = "INSERT INTO equips (mac, version, admins, maquina, nomusuari, connexions, data_restauracio, restriccio, snap_installat, snap_vpns, snap_opera, windows, serial, model)
VALUES ('$mac', '$version', '$admins', '$maquina', '$nomusuari', '$connexions', '$data_restauracio', '$restriccio', '$snap_installat', '$snap_vpns', '$snap_opera', '$windows', '$serial', '$model')";

if ($conn->query($sql) === TRUE) {
    echo "Datos insertados correctamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

header("Location: datos.php");
exit();
?>