<?php
$servidor = "localhost";
$usuario_bd = "root"; // Cambia esto si tu usuario es diferente
$contrasena_bd = ""; // Cambia esto si tienes contraseña
$base_datos = "inco";

$conn = new mysqli($servidor, $usuario_bd, $contrasena_bd, $base_datos);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
