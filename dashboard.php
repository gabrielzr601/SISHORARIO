<?php
session_start();

// Verifica si el profesor ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Fondo claro */
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #C21807; /* Rojo suave */
            color: #FFFFFF; /* Blanco */
            padding: 15px;
            text-align: center;
            position: relative;
        }
        .toggle-btn {
            position: absolute;
            left: 15px;
            top: 15px;
            background-color: #FFA500; /* Naranja */
            color: black;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 18px;
            z-index: 1000;
        }
        .sidebar {
            background-color: #003366; /* Azul oscuro */
            color: #FFFFFF; /* Blanco */
            position: fixed;
            top: 0;
            left: -250px; /* Oculta el sidebar inicialmente */
            width: 250px;
            height: 100%;
            padding: 20px;
            transition: left 0.3s;
            box-sizing: border-box;
        }
        .sidebar.active {
            left: 0; /* Muestra el sidebar cuando tiene la clase 'active' */
        }
        .sidebar a {
            display: block;
            color: #FFFFFF; /* Enlaces en blanco */
            padding: 10px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #FFA500; /* Naranja para el hover */
            color: black; /* Cambia a negro en hover */
        }
        .content {
            margin-left: 20px; /* Ajuste inicial con el sidebar oculto */
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .content.sidebar-active {
            margin-left: 270px; /* Ajusta el margen cuando el sidebar está desplegado */
        }
        .content h2 {
            margin-top: 0;
        }
        .card {
            background-color: #F5F5F5; /* Gris claro */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .logout {
            background-color: #B22222; /* Rojo oscuro */
            color: white;
            text-align: center;
            border-radius: 4px;
            margin-top: 20px;
            padding: 10px; /* Añadir padding para centrado */
            transition: background-color 0.3s;
            position: relative;
            left: 0;
            width: calc(100% - 20px); /* Ajustar el ancho para que no sobresalga */
            box-sizing: border-box;
        }
        .logout a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .logout:hover {
            background-color: #FF4500; /* Rojo brillante en hover */
        }
    </style>
</head>
<body>
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <h1>Bienvenido, <?php echo $_SESSION['nombre_usuario']; ?>!</h1>
    </div>
    
    <div id="sidebar" class="sidebar">
        <h3 style="margin-top: 60px;">Menú</h3> 
        <a href="materias.php">Agregar materias</a>
        <a href="agregar_materia.php">Agregar Profesores</a>
        <a href="agregar_seccion.php">Agregar Secciones</a>
        <div class="logout">
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div id="content" class="content">
        <h2>Dashboard</h2>
        <div class="card">
            <p>Contenido del dashboard...</p>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var content = document.getElementById('content');
            
            // Alternar la clase 'active' en el sidebar y ajustar el contenido
            sidebar.classList.toggle('active');
            content.classList.toggle('sidebar-active');
        }
    </script>
</body>
</html>
