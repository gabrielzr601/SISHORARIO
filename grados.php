<?php 
session_start();

// Verifica si el profesor ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}

// Incluir la conexión a la base de datos
include('config.php');

// Definir una variable para mostrar el mensaje
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['año']) && isset($_POST['especialidad']) && isset($_POST['seccion'])) {
    $año = ucwords(strtolower($_POST['año']));
    $especialidad = ucwords(strtolower($_POST['especialidad']));
    $seccion = ucwords(strtolower($_POST['seccion']));

    // Validar que no contengan números en el campo año (si no se espera eso)
    if (preg_match("/[0-9]/", $especialidad) || preg_match("/[0-9]/", $seccion)) {
        $mensaje = "La especialidad y la sección no deben contener números.";
    } else {
        // Verificar si el grado ya existe
        $sql_check = "SELECT COUNT(*) AS total FROM grados WHERE año = '$año' AND especialidad = '$especialidad' AND seccion = '$seccion'";
        $result_check = $conn->query($sql_check);
        $row_check = $result_check->fetch_assoc();

        if ($row_check['total'] > 0) {
            $mensaje = "El grado para '$año', '$especialidad', sección '$seccion' ya existe. Por favor ingrese una combinación diferente.";
        } else {
            // Si se está editando un grado existente
            if (isset($_POST['id_grado']) && !empty($_POST['id_grado'])) {
                $id_grado = $_POST['id_grado'];
                $sql = "UPDATE grados SET año='$año', especialidad='$especialidad', seccion='$seccion' WHERE id_grado='$id_grado'";
                if ($conn->query($sql) === TRUE) {
                    $mensaje = "Grado actualizado con éxito!";
                } else {
                    $mensaje = "Error al actualizar: " . $conn->error;
                }
            } else {
                // Si es un nuevo grado
                $sql = "INSERT INTO grados (año, especialidad, seccion) VALUES ('$año', '$especialidad', '$seccion')";
                if ($conn->query($sql) === TRUE) {
                    $mensaje = "Grado agregado con éxito!";
                } else {
                    $mensaje = "Error al agregar: " . $conn->error;
                }
            }
        }
    }
}

// Eliminar grado
if (isset($_GET['eliminar'])) {
    $id_grado = $_GET['eliminar'];
    $sql = "DELETE FROM grados WHERE id_grado='$id_grado'";
    if ($conn->query($sql) === TRUE) {
        $mensaje = "Grado eliminado con éxito!";
    } else {
        $mensaje = "Error al eliminar: " . $conn->error;
    }
}

// Obtener grados
$sql = "SELECT * FROM grados";
$result = $conn->query($sql);

$por_pagina = 6; // Número de registros por página
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$offset = ($pagina - 1) * $por_pagina;

// Obtener el total de grados
$sql_total = "SELECT COUNT(*) AS total FROM grados";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_grados = $row_total['total'];

// Calcular el total de páginas
$total_paginas = ceil($total_grados / $por_pagina);

// Obtener grados con paginación
$sql = "SELECT * FROM grados LIMIT $por_pagina OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<title>Agregar Maestro</title>
<style>
/* Estilos generales */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .header {
        background-color: #C21807;
        color: #FFFFFF;
        padding: 15px;
        text-align: center;
        position: relative;
    }
    .toggle-btn {
        position: absolute;
        left: 15px;
        top: 15px;
        background-color: #FFA500;
        color: black;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 18px;
        z-index: 1000;
    }
    .sidebar {
        background-color: #003366;
        color: #FFFFFF;
        position: fixed;
        top: 0;
        left: -250px;
        width: 250px;
        height: 100%;
        padding: 20px;
        transition: left 0.3s;
        box-sizing: border-box;
    }
    .sidebar.active {
        left: 0;
    }
    .sidebar a {
        display: block;
        color: #FFFFFF;
        padding: 10px;
        text-decoration: none;
        margin-bottom: 10px;
        border-radius: 4px;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    .sidebar a:hover {
        background-color: #FFA500;
        color: black;
    }
    .content {
        margin-left: 20px;
        padding: 20px;
        transition: margin-left 0.3s;
    }
    .content.sidebar-active {
        margin-left: 270px;
    }
    .content h2 {
        margin-top: 0;
        text-align: center;
    }
    .card {
        background-color: #F5F5F5;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .logout {
        background-color: #B22222;
        color: white;
        text-align: center;
        border-radius: 4px;
        margin-top: 20px;
        padding: 10px;
        transition: background-color 0.3s;
    }
    .logout a {
        color: white;
        text-decoration: none;
        display: block;
    }
    .logout:hover {
        background-color: #FF4500;
    }
    .pagination {
        margin-top: 20px;
        text-align: center;
    }
    .pagination a {
        color: black;
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid #ddd;
        margin: 0 4px;
    }
    .pagination a:hover {
        background-color: #ddd;
    }
    .pagination .active {
        background-color: #FF5722;
        color: white;
    }

    /* Estilo de la ventana emergente de éxito */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
        padding-right: 20px;
    }
    .modal-content {
        background-color: #4CAF50;
        margin: 5% auto;
        padding: 60px;
        padding-right: 75px;
        border: 1px solid #888;
        width: 50%;
        max-width: 400px;
        text-align: center;
        color: white;
        border-radius: 8px;
        position: relative;
    }
    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        top: 5px;
        right: 15px;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Estilos para la tabla */
    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        text-align: center;
        
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th, td {
        padding: 12px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #f1f1f1;
    }

    /* Botones */
    .button {
        background-color: #C21807;
        color: white;
        border: none;
        padding: 12px 20px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .button:hover {
        background-color: #FF5722;
    }

    /* Modal de agregar materia */
    #myModal .modal-content {
        width: 100%;
    }

    /* Para centrar el contenido */
    .centered {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
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
        <a href="materias.php">Materias</a>
        <a href="maestros.php">Profesores</a>
        <a href="grados.php">Grados</a>
        <a href="horarios.php">Horarios</a>
        <div class="logout">
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div id="content" class="content">
        <h2>Agregar Grado</h2>
        <button class="button" onclick="openModal()">Agregar Grado</button>

        <!-- Modal de agregar/editar maestro -->
        <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Agregar Grado</h2>
            <form action="grados.php?pagina=<?php echo $pagina; ?>" method="POST">
                <input type="hidden" name="id_grado" id="id_grado">
                
                <input type="text" 
                    name="año" 
                    id="añoGrado" 
                    placeholder="Año de Grado" 
                    required 
                    style="width: 100%; padding: 10px; border-radius: 5px; margin-bottom: 20px;"
                    oninput="validarSinNumeros(this)">
                
                <input type="text" 
                    name="especialidad" 
                    id="especialidadGrado" 
                    placeholder="Especialidad" 
                    required 
                    style="width: 100%; padding: 10px; border-radius: 5px; margin-bottom: 20px;"
                    oninput="validarSinNumeros(this)">

                <input type="text" 
                    name="seccion" 
                    id="seccionGrado" 
                    placeholder="Sección" 
                    required 
                    style="width: 100%; padding: 10px; border-radius: 5px;"
                    
                    oninput="validarSeccion(this), validarSinNumeros(this)">
                
                <button type="submit" class="button">Guardar</button>
                <button type="button" class="button" onclick="closeModal()">Cancelar</button>
            </form>
        </div>
    </div>


    <!-- Ventana Emergente de Éxito -->
    <?php if ($mensaje != ''): ?>
    <div id="successModal" class="modal" style="display:block;">
        <div class="modal-content">
            <span class="close" onclick="closeSuccessModal()">&times;</span>
            <h2><?php echo $mensaje; ?></h2>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista de Maestros -->
    <div class="card">
        <h2>Lista de Maestros</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Año</th>
                    <th>Especialidad</th>
                    <th>Seccion</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_grado']; ?></td>
                    <td><?php echo ucwords(strtolower($row['año'])); ?></td>
                    <td><?php echo ucwords(strtolower($row['especialidad'])); ?></td>
                    <td><?php echo ucwords(strtolower($row['seccion'])); ?></td>
                    <td>
                        <a href="?eliminar=<?php echo $row['id_grado']; ?>&pagina=<?php echo $pagina; ?>" class="fa fa-trash-alt" onclick="return confirm('¿Está seguro de eliminar este maestro?');" title="Eliminar"></a>
                        |
                        <a href="#" class="fa fa-edit" title="Editar" style="color: blue;" onclick="openEditModal('<?php echo $row['id_grado']; ?>', '<?php echo $row['año']; ?>', '<?php echo $row['especialidad']; ?>', '<?php echo $row['seccion']; ?>')"></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Paginación -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="?pagina=<?php echo $i; ?>" class="<?php echo ($pagina == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var content = document.getElementById('content');
        
        sidebar.classList.toggle('active');
        content.classList.toggle('sidebar-active');
    }

    var modal = document.getElementById("myModal");

    function openModal() 
    {
        document.getElementById("id_grado").value = "";
        document.getElementById("añoGrado").value = "";
        document.getElementById("especialidadGrado").value = "";
        document.getElementById("seccionGrado").value = "";
        document.getElementById("modalTitle").textContent = "Agregar Grado";
        modal.style.display = "block";
    }

    function openEditModal(id_grado, año, especialidad, seccion) 
    {
        document.getElementById("id_grado").value = id_grado;
        document.getElementById("añoGrado").value = año;
        document.getElementById("especialidadGrado").value = especialidad;
        document.getElementById("seccionGrado").value = seccion;
        document.getElementById("modalTitle").textContent = "Editar Grado";
        modal.style.display = "block";
    }


    function closeModal() {
        modal.style.display = "none";
    }

    var successModal = document.getElementById("successModal");

    function closeSuccessModal() {
        successModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        if (event.target == successModal) {
            successModal.style.display = "none";
        }
    }
    function validarSinNumeros(input) {
        input.value = input.value.replace(/[0-9]/g, ''); // Elimina cualquier número que se escriba
    }
    function validarSeccion(input) {
    // Permite solo una letra (a-z o A-Z)
    input.value = input.value.replace(/[^a-zA-Z]/g, ''); // Elimina caracteres no alfabéticos
    if (input.value.length > 1) {
        input.value = input.value.charAt(0); // Limita a solo 1 letra
    }
    }  
    
</script>
</body>
</html>
