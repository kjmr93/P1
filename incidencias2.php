<?php
// Verificar si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_COOKIE['usuario']) || !isset($_COOKIE['es_administrador']) || $_COOKIE['es_administrador'] !== "1") {
    header("Location: login.php?error=Debe%20iniciar%20sesión%20como%20administrador%20para%20acceder.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrar Historial de Incidencias</title>
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
        form {
            margin: 20px;
        }
        input[type="text"], input[type="date"], select {
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
        #toggle-opciones {
            position: relative;
            display: block;
            margin: 10px auto 0 auto;
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
        }
        #toggle-opciones:hover {
            background-color: #357ab8;
        }
    </style>
</head>
<body>
    <h1>
        <a href="inicio.php" class="inicio-button">Inicio</a>
        Borrar Historial de Incidencias
    </h1>
    <button id="toggle-opciones">
        Opciones
    </button>
    <div id="opciones" style="display: none; margin-top: 20px; margin-left: 20px">
        <form id="borrar-incidencias-form" method="POST" action="borrar_incidencias.php" style="margin-bottom: 20px;">
            <label for="fecha-inicio" style="font-weight: bold;">Fecha de inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha-inicio" style="margin-bottom: 10px;">

            <label for="fecha-fin" style="font-weight: bold;">Fecha de fin:</label>
            <input type="date" name="fecha_fin" id="fecha-fin" style="margin-bottom: 20px;">

            <button type="submit" style="background-color: #4a90e2; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;">
                Borrar historial de incidencias
            </button>
        </form>
    </div>
    <script>
        document.getElementById('toggle-opciones').addEventListener('click', function () {
            const opcionesDiv = document.getElementById('opciones');
            if (opcionesDiv.style.display === 'none') {
                opcionesDiv.style.display = 'block';
            } else {
                opcionesDiv.style.display = 'none';
            }
        });

        document.getElementById('borrar-incidencias-form').addEventListener('submit', function (event) {
            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;

            let mensaje = '';

            // Validar que ambos campos de fecha estén completos o ninguno
            if ((fechaInicio && !fechaFin) || (!fechaInicio && fechaFin)) {
                alert('Debe especificar tanto la fecha de inicio como la fecha de fin, o dejar ambos campos vacíos.');
                event.preventDefault(); // Detener el envío del formulario
                return;
            }

            // Mensaje de confirmación según las fechas
            if (fechaInicio && fechaFin) {
                mensaje = `Se borrarán las incidencias entre las fechas especificadas (${fechaInicio} y ${fechaFin}), ¿desea continuar?`;
            } else {
                mensaje = 'AVISO: Se va a borrar el historial de incidencias completo, ¿desea continuar?';
            }

            if (!confirm(mensaje)) {
                event.preventDefault(); // Detener el envío del formulario si el usuario cancela
            }
        });
    </script>
</body>
</html>