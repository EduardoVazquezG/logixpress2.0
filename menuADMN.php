<?php
session_start();
require('includes/config/conection.php');
$db = connectTo2DB();

// Función para mostrar el formulario de empleados
function formularioEmpleado($empleado = null) {
    global $db;

    $puestos = [];
    $query = "SELECT codigo, descripcion FROM PUESTO";
    $result = mysqli_query($db, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $puestos[] = $row;
    }

    $num = $empleado['num'] ?? '';
    $nombre = $empleado['nombre'] ?? '';
    $primerApe = $empleado['primerApe'] ?? '';
    $segundoApe = $empleado['segundoApe'] ?? '';
    $telefono = $empleado['telefono'] ?? '';
    $email = $empleado['email'] ?? '';
    $password = '';
    $puesto = $empleado['puesto'] ?? '';
    ?>
    <h3>Formulario de Empleado</h3>
    <form action="" method="post">
        <input type="hidden" name="num" value="<?php echo $num; ?>">
        <label>Nombre: <input type="text" name="nombre" value="<?php echo $nombre; ?>"></label>
        <label>Primer Apellido: <input type="text" name="primerApe" value="<?php echo $primerApe; ?>"></label>
        <label>Segundo Apellido: <input type="text" name="segundoApe" value="<?php echo $segundoApe; ?>"></label>
        <label>Teléfono: <input type="text" name="telefono" value="<?php echo $telefono; ?>"></label>
        <label>Email: <input type="email" name="email" value="<?php echo $email; ?>"></label>
        <label>Password: <input type="password" name="password" value="<?php echo $password; ?>"></label>
        <label>Puesto:
            <select name="puesto">
                <option value="" selected>Seleccione un puesto</option>
                <?php foreach ($puestos as $p) : ?>
                    <option value="<?php echo $p['codigo']; ?>" <?php echo $puesto == $p['codigo'] ? 'selected' : ''; ?>>
                        <?php echo $p['descripcion']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" name="accion" value="guardar_empleado">Guardar</button>
    </form>
    <?php
}

// Función para mostrar el formulario de vehículos
function formularioVehiculo($vehiculo = null) {
    $placa = $vehiculo['placa'] ?? '';
    $numSerie = $vehiculo['numSerie'] ?? '';
    $marca = $vehiculo['marca'] ?? '';
    $modelo = $vehiculo['modelo'] ?? '';
    $anio = $vehiculo['anio'] ?? '';
    $tipoCarga = $vehiculo['tipoCarga'] ?? '';
    $categoria = $vehiculo['categoria'] ?? '';
    $capacidad = $vehiculo['capacidad'] ?? '';
    $disponibilidad = $vehiculo['disponibilidad'] ?? '';
    ?>
    <h3>Formulario de Vehículo</h3>
    <form action="" method="post">
        <label>Placa: <input type="text" name="placa" value="<?php echo $placa; ?>"></label>
        <label>Número de Serie: <input type="text" name="numSerie" value="<?php echo $numSerie; ?>"></label>
        <label>Marca: <input type="text" name="marca" value="<?php echo $marca; ?>"></label>
        <label>Modelo: <input type="text" name="modelo" value="<?php echo $modelo; ?>"></label>
        <label>Año: <input type="number" name="anio" value="<?php echo $anio; ?>"></label>
        <label>Tipo de Carga: <input type="text" name="tipoCarga" value="<?php echo $tipoCarga; ?>"></label>
        <label>Categoría: <input type="text" name="categoria" value="<?php echo $categoria; ?>"></label>
        <label>Capacidad: <input type="number" name="capacidad" value="<?php echo $capacidad; ?>"></label>
        <label>Disponibilidad: <input type="text" name="disponibilidad" value="<?php echo $disponibilidad; ?>"></label>
        <button type="submit" name="accion" value="guardar_vehiculo">Guardar</button>
    </form>
    <?php
}

// Función para mostrar el formulario de remolques
function formularioRemolque($remolque = null) {
    $numSerie = $remolque['numSerie'] ?? '';
    $marca = $remolque['marca'] ?? '';
    $modelo = $remolque['modelo'] ?? '';
    $anio = $remolque['anio'] ?? '';
    $tipoCarga = $remolque['tipoCarga'] ?? '';
    $capacidad = $remolque['capacidad'] ?? '';
    $disponibilidad = $remolque['disponibilidad'] ?? '';
    ?>
    <h3>Formulario de Remolque</h3>
    <form action="" method="post">
        <label>Número de Serie: <input type="text" name="numSerie" value="<?php echo $numSerie; ?>"></label>
        <label>Marca: <input type="text" name="marca" value="<?php echo $marca; ?>"></label>
        <label>Modelo: <input type="text" name="modelo" value="<?php echo $modelo; ?>"></label>
        <label>Año: <input type="number" name="anio" value="<?php echo $anio; ?>"></label>
        <label>Tipo de Carga: <input type="text" name="tipoCarga" value="<?php echo $tipoCarga; ?>"></label>
        <label>Capacidad: <input type="number" name="capacidad" value="<?php echo $capacidad; ?>"></label>
        <label>Disponibilidad: <input type="text" name="disponibilidad" value="<?php echo $disponibilidad; ?>"></label>
        <button type="submit" name="accion" value="guardar_remolque">Guardar</button>
    </form>
    <?php
}

// Lógica de inserción y verificación de duplicados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'logout') {
        // Cerrar sesión
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }

    $accion = $_POST['accion'];
    switch ($accion) {
        case 'guardar_empleado':
            $nombre = $_POST['nombre'];
            $primerApe = $_POST['primerApe'];
            $segundoApe = $_POST['segundoApe'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $puesto = $_POST['puesto'];

            $stmt = $db->prepare("SELECT email FROM EMPLEADO WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Error: El correo ya está registrado para otro empleado.";
            } else {
                $stmt->close();
                $stmt = $db->prepare("INSERT INTO EMPLEADO (nombre, primerApe, segundoApe, telefono, email, password, puesto) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $nombre, $primerApe, $segundoApe, $telefono, $email, $password, $puesto);

                if ($stmt->execute()) {
                    echo "Empleado insertado correctamente.";
                } else {
                    echo "Error al insertar el empleado: " . $stmt->error;
                }
                $stmt->close();
            }
            break;

        case 'guardar_vehiculo':
            $placa = $_POST['placa'];
            $numSerie = $_POST['numSerie'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $anio = $_POST['anio'];
            $tipoCarga = $_POST['tipoCarga'];
            $categoria = $_POST['categoria'];
            $capacidad = $_POST['capacidad'];
            $disponibilidad = $_POST['disponibilidad'];

            $stmt = $db->prepare("SELECT placa FROM VEHICULO WHERE placa = ? OR numSerie = ?");
            $stmt->bind_param("ss", $placa, $numSerie);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Error: La placa o el número de serie ya están registrados para otro vehículo.";
            } else {
                $stmt->close();
                $stmt = $db->prepare("INSERT INTO VEHICULO (placa, numSerie, marca, modelo, anio, tipoCarga, categoria, capacidad, disponibilidad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssiiiii", $placa, $numSerie, $marca, $modelo, $anio, $tipoCarga, $categoria, $capacidad, $disponibilidad);

                if ($stmt->execute()) {
                    echo "Vehículo insertado correctamente.";
                } else {
                    echo "Error al insertar el vehículo: " . $stmt->error;
                }
                $stmt->close();
            }
            break;

        case 'guardar_remolque':
            $numSerie = $_POST['numSerie'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $anio = $_POST['anio'];
            $tipoCarga = $_POST['tipoCarga'];
            $capacidad = $_POST['capacidad'];
            $disponibilidad = $_POST['disponibilidad'];

            $stmt = $db->prepare("SELECT numSerie FROM REMOLQUE WHERE numSerie = ?");
            $stmt->bind_param("s", $numSerie);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Error: El número de serie ya está registrado para otro remolque.";
            } else {
                $stmt->close();
                $stmt = $db->prepare("INSERT INTO REMOLQUE (numSerie, marca, modelo, anio, tipoCarga, capacidad, disponibilidad) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssiiii", $numSerie, $marca, $modelo, $anio, $tipoCarga, $capacidad, $disponibilidad);

                if ($stmt->execute()) {
                    echo "Remolque insertado correctamente.";
                } else {
                    echo "Error al insertar el remolque: " . $stmt->error;
                }
                $stmt->close();
            }
            break;
    }
}

// Selección de sección para mostrar formulario
$section = $_GET['section'] ?? 'empleados';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="container">
    <nav>
        <div class="logo-container">
            <a href="index.php">
                <img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo">
            </a>
        </div>
        <ul>
            <li><a href="?section=empleados">Empleados</a></li>
            <li><a href="?section=vehiculos">Vehículos</a></li>
            <li><a href="?section=remolques">Remolques</a></li>
        </ul>
        <!-- Botón de Logout -->
        <form action="" method="post" >
            <button type="submit" name="accion" value="logout">Cerrar Sesión</button>
        </form>
    </nav>

    <div class="main-content">
        <?php
        switch ($section) {
            case 'vehiculos':
                formularioVehiculo();
                break;
            case 'remolques':
                formularioRemolque();
                break;
            default:
                formularioEmpleado();
                break;
        }
        ?>
    </div>
</div>
</body>
</html>
