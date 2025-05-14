<?php
$servername = "172.21.23.245";
$username = "dbportatils";
$password = "R00tR00t@dbportatils";
$dbname = "dbportatils";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>