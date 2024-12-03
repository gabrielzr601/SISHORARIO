<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inco";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultas para obtener datos únicos
$queryYear = "SELECT DISTINCT año FROM grados ORDER BY año";
$querySpecialty = "SELECT DISTINCT especialidad FROM grados ORDER BY especialidad";
$querySection = "SELECT DISTINCT seccion FROM grados ORDER BY seccion";

$years = $conn->query($queryYear);
$specialties = $conn->query($querySpecialty);
$sections = $conn->query($querySection);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combobox de Grados</title>
</head>
<body>
    <form action="" method="POST">
        <!-- Combobox Año -->
        <label for="year">Año:</label>
        <select name="year" id="year">
            <option value="">Seleccione un año</option>
            <?php
            if ($years->num_rows > 0) {
                while ($row = $years->fetch_assoc()) {
                    echo "<option value='" . $row['año'] . "'>" . $row['año'] . "</option>";
                }
            }
            ?>
        </select>
        <br><br>

        <!-- Combobox Especialidad -->
        <label for="specialty">Especialidad:</label>
        <select name="specialty" id="specialty">
            <option value="">Seleccione una especialidad</option>
            <?php
            if ($specialties->num_rows > 0) {
                while ($row = $specialties->fetch_assoc()) {
                    echo "<option value='" . $row['especialidad'] . "'>" . $row['especialidad'] . "</option>";
                }
            }
            ?>
        </select>
        <br><br>

        <!-- Combobox Sección -->
        <label for="section">Sección:</label>
        <select name="section" id="section">
            <option value="">Seleccione una sección</option>
            <?php
            if ($sections->num_rows > 0) {
                while ($row = $sections->fetch_assoc()) {
                    echo "<option value='" . $row['seccion'] . "'>" . $row['seccion'] . "</option>";
                }
            }
            ?>
        </select>
        <br><br>

        <button type="submit">Enviar</button>
    </form>

    <?php
    // Procesar datos seleccionados
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $selectedYear = $_POST['year'] ?? null;
        $selectedSpecialty = $_POST['specialty'] ?? null;
        $selectedSection = $_POST['section'] ?? null;

        echo "<h3>Valores seleccionados:</h3>";
        echo "Año: " . ($selectedYear ?: "No seleccionado") . "<br>";
        echo "Especialidad: " . ($selectedSpecialty ?: "No seleccionada") . "<br>";
        echo "Sección: " . ($selectedSection ?: "No seleccionada") . "<br>";
    }
    ?>

</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
