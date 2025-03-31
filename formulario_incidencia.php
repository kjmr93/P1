<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inserción de Incidencias</title>
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
    <h2>Formulario de Inserción de Incidencias</h2>
    <form method="POST" action="insertar_incidencia.php">
        <table id="insert-table">
            <thead>
                <tr>
                    <th>Incidencia</th>
                    <th>Usuario</th>
                    <th>Equipo</th>
                    <th>Clase</th>
                    <th>Restricción Equipo</th>
                    <th>Restricción Usuario</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="incidencia" placeholder="Incidencia"></td>
                    <td><input type="text" name="usuario" placeholder="Usuario"></td>
                    <td><input type="text" name="equipo" placeholder="Equipo"></td>
                    <td><input type="text" name="clase" placeholder="Clase"></td>
                    <td><input type="text" name="restriccion_equipo" placeholder="Restricción Equipo"></td>
                    <td><input type="text" name="restriccion_usuario" placeholder="Restricción Usuario"></td>
                </tr>
            </tbody>
        </table>
        <button type="submit">Insertar Incidencia</button>
    </form>
</body>
</html>