<?php
// Eliminar cookies
setcookie("usuario", "", time() - 3600, "/");
setcookie("es_administrador", "", time() - 3600, "/");

// Redirigir al login
header("Location: login.php");
exit();
?>