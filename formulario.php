<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inserción de Datos</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h2 {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
            margin: 0;
        }
        table {
            width: auto;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            width: 150px;
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
        form {
            margin: 20px;
        }
        input[type="text"] {
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
            display: block;
            margin: 20px auto;
        }
        button:hover {
            background-color: #357ab8;
        }
    </style>
</head>
<body>
    <h2>Formulario de Inserción de Datos</h2>
    <form method="POST" action="insertar.php">
        <table id="insert-table">
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
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="mac" placeholder="MAC"></td>
                    <td><input type="text" name="version" placeholder="Versión"></td>
                    <td><input type="text" name="admins" placeholder="Admins"></td>
                    <td><input type="text" name="maquina" placeholder="Máquina"></td>
                    <td><input type="text" name="nomusuari" placeholder="Nombre de Usuario"></td>
                    <td><input type="text" name="connexions" placeholder="Conexiones"></td>
                    <td><input type="text" name="data_restauracio" placeholder="Fecha de Restauración"></td>
                    <td><input type="text" name="restriccio" placeholder="Restricción"></td>
                    <td><input type="text" name="snap_installat" placeholder="Snap Instalado"></td>
                    <td><input type="text" name="snap_vpns" placeholder="Snap VPNs"></td>
                    <td><input type="text" name="snap_opera" placeholder="Snap Opera"></td>
                    <td><input type="text" name="windows" placeholder="Windows"></td>
                    <td><input type="text" name="serial" placeholder="Serial"></td>
                    <td><input type="text" name="model" placeholder="Modelo"></td>
                </tr>
            </tbody>
        </table>
        <button type="submit">Insertar Datos</button>
    </form>
</body>
</html>