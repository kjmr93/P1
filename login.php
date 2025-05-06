<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }
        .login-container h1 {
            margin-bottom: 20px;
            color: #4a90e2;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .login-container button {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        .login-container button:hover {
            background-color: #357ab8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="procesar_login.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuario">
            <input type="password" name="contrasena" placeholder="Contraseña">
            <?php
            if (isset($_GET['error'])) {
                echo '<p style="color: red; text-align: center;">' . htmlspecialchars($_GET['error']) . '</p>';
            }
            ?>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>