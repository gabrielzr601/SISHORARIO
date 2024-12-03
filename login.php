<?php
// Puedes incluir aquí cualquier lógica PHP, si es necesario.
session_start();
if (isset($_SESSION['nombre_usuario'])) {
    // Si el usuario ya ha iniciado sesión, redirigir al dashboard.
    header("Location: materias.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Profesores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #d8ebf7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            margin-bottom: 50px;
            text-align: center;
        }
        .login-container input,
        .login-container button {
            width: calc(100% - 20px); /* Ajustamos el tamaño total */
            padding: 10px;
            margin-left: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box; /* Asegura que el padding no afecte el tamaño total */
        }
        .login-container input {
            border: 1px solid #ccc;
        }
        .login-container button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar sesión - Profesores</h2>
        <form action="procesar_login.php" method="POST" autocomplete="off">
            <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>
