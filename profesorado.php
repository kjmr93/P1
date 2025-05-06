<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Paginación
$results_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Ordenación
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'usuario';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';

// Construir la consulta SQL con paginación y ordenación
$sql = "SELECT * FROM profesorado ORDER BY $order_by $order_dir LIMIT $start_from, $results_per_page";

// Ejecutar la consulta y verificar errores
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el número total de resultados para la paginación
$total_sql = "SELECT COUNT(*) FROM profesorado";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $results_per_page);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Profesorado</title>
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
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
        .show-password-btn {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .show-password-btn:hover {
            background-color: #357ab8;
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
        .show-password-btn {
            margin-left: 10px;
            background-color: #4a90e2;
        }
    </style>
    <script>
        function togglePassword(button, password) {
            const td = button.parentElement;
            if (button.textContent === "Mostrar contraseña") {
                td.textContent = password;
                td.appendChild(button);
                button.textContent = "Ocultar contraseña";
            } else {
                td.textContent = "********";
                td.appendChild(button);
                button.textContent = "Mostrar contraseña";
            }
        }
    </script>
</head>
<body>
<h1>
    <a href="inicio.php" class="inicio-button">Inicio</a>
    Profesorado
</h1>

<form action="insertar_profesorado.php" method="POST" onsubmit="return validarFormulario()" style="width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
    <h2 style="text-align: center; color: #4a90e2;">Añadir Nuevo Usuario</h2>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right; width: 30%;"><label for="usuario">Usuario:</label></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><input type="text" id="usuario" name="usuario" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;"></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;"><label for="password">Contraseña:</label></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><input type="password" id="password" name="password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;"></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;"><label for="confirm_password">Confirmar Contraseña:</label></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;"></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;"><label for="rol">Rol:</label></td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <select id="rol" name="rol" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="0">Administrador</option>
                    <option value="1">Usuario</option>
                </select>
            </td>
        </tr>
    </table>
    <div style="text-align: center; margin-top: 20px;">
        <button type="submit" style="background-color: #4a90e2; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Añadir Usuario</button>
    </div>
</form>

<script>
    function validarFormulario() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden. Por favor, inténtelo de nuevo.');
            return false;
        }
        return true;
    }
</script>

<table>
    <thead>
        <tr>
            <th>Usuario<br>
                <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'usuario', 'order_dir' => 'asc'])) ?>" class="asc">&#9650;</a>
                <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'usuario', 'order_dir' => 'desc'])) ?>" class="desc">&#9660;</a>
            </th>
            <th>Contraseña</th>
            <th>Rol</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $usuario = htmlspecialchars($row['usuario']);
                $password = htmlspecialchars($row['password']);
                $rol = $row['administrador'] == 0 ? "Administrador" : "Usuario";

                echo "<tr>";
                echo "<td>$usuario</td>";
                echo "<td>" . "********" . " <button class='show-password-btn' onclick=\"togglePassword(this, '$password')\">Mostrar contraseña</button></td>";
                echo "<td>$rol</td>";
                echo "<td>
                    <button class='modify-user-btn' onclick=\"toggleForm('form-$id')\">Modificar usuario</button>
                    <div id='form-$id' class='modify-form' style='display: none; margin-top: 10px;'>
                        <form action='actualizar_profesorado.php' method='POST' onsubmit='return validarFormulario(\"$id\")' style='border: 1px solid #ddd; padding: 10px; border-radius: 5px; background-color: #f9f9f9;'>
                            <input type='hidden' name='id' value='$id'>
                            <label for='usuario-$id'>Usuario:</label>
                            <input type='text' id='usuario-$id' name='usuario' value='$usuario' required style='width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;'>
                            <label for='password-$id'>Contraseña:</label>
                            <input type='password' id='password-$id' name='password' required style='width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;'>
                            <label for='confirm_password-$id'>Confirmar Contraseña:</label>
                            <input type='password' id='confirm_password-$id' name='confirm_password' required style='width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;'>
                            <label for='rol-$id'>Rol:</label>
                            <select id='rol-$id' name='rol' required style='width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;'>
                                <option value='0' " . ($rol === "Administrador" ? "selected" : "") . ">Administrador</option>
                                <option value='1' " . ($rol === "Usuario" ? "selected" : "") . ">Usuario</option>
                            </select>
                            <button type='submit' style='background-color: #4a90e2; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Guardar cambios</button>
                        </form>
                    </div>
                </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay datos disponibles</td></tr>";
        }
        ?>
    </tbody>
</table>

<script>
    function toggleForm(formId) {
        const form = document.getElementById(formId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function validarFormulario(id) {
        const password = document.getElementById(`password-${id}`).value;
        const confirmPassword = document.getElementById(`confirm_password-${id}`).value;

        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden. Por favor, inténtelo de nuevo.');
            return false;
        }
        return true;
    }
</script>

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