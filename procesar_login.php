<?php
session_start();
require 'config.php'; // Archivo donde se configura la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena'];

    // Consulta a la base de datos para verificar el nombre de usuario con validación sensible a mayúsculas/minúsculas
    $sql = "SELECT * FROM profesores WHERE BINARY nombre_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $profesor = $resultado->fetch_assoc();
        
        // Verifica si la contraseña cifrada coincide
        if (md5($contrasena) == $profesor['contrasena']) {
            $_SESSION['nombre_usuario'] = $profesor['nombre_usuario'];
            
            // Redirige al dashboard con un mensaje de bienvenida
            echo "<script>
                    alert('¡Bienvenido, " . $_SESSION['nombre_usuario'] . "!');
                    window.location.href = 'materias.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta.'); window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('El usuario no existe.'); window.location.href = 'login.php';</script>";
    }
}
?>
