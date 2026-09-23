<?php
// tecnicoEquipos.php
session_start();
$pagina_activa = 'equipos'; // Para el menú
require 'conexion.php';

// 1. Seguridad: Proteger la página
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || $rol_usuario_logueado != 'tecnico') {
    header("Location: index.php?error=Acceso denegado");
    exit;
}
$id_usuario_logueado = $_SESSION['id_usuario'];

// --- LÓGICA DE ORDENACIÓN ---
$columna = isset($_GET['col']) ? $_GET['col'] : 'planta'; // Por defecto ordenamos por planta
$direccion = isset($_GET['dir']) && $_GET['dir'] == 'DESC' ? 'DESC' : 'ASC';
$nueva_dir = ($direccion == 'ASC') ? 'DESC' : 'ASC'; // Invierte dirección para el próximo clic

// Definimos el SQL según lo que se haya clicado
switch ($columna) {
    case 'aula':
        $sql_orden = "e.Aula $direccion, e.Planta ASC";
        break;
    case 'asignado':
        $sql_orden = "u.Nombre $direccion, u.Apellidos $direccion, e.Aula ASC";
        break;
    case 'planta':
    default:
        $sql_orden = "e.Planta $direccion, e.Aula ASC";
        break;
}

// Función para pintar la flechita
function flecha($col_actual, $col_seleccionada, $dir) {
    if ($col_actual == $col_seleccionada) {
        return ($dir == 'ASC') ? ' ▲' : ' ▼';
    }
    return '';
}


// 2. Lógica para mostrar mensajes de éxito/error
$mensaje = '';
if (isset($_GET['success'])) {
    $mensaje = "<p class='mensaje success'>Equipo añadido correctamente.</p>";
}
if (isset($_GET['error'])) {
    $mensaje = "<p class='mensaje error'>" . htmlspecialchars($_GET['error']) . "</p>";
}

// 3. Consulta para el desplegable de "Asignar a"
$sql_usuarios = "SELECT IdUsuario, Nombre, Apellidos, Rol FROM Usuario ORDER BY Rol, Apellidos";
$resultado_usuarios = $conn->query($sql_usuarios);

// 4. Consultas para los desplegables del formulario (PLANTAS)
$sql_plantas = "SELECT DISTINCT Planta FROM Equipo ORDER BY Planta";
$res_plantas = $conn->query($sql_plantas);
$plantas = [];
if ($res_plantas) {
    while ($fila = $res_plantas->fetch_assoc()) {
        // Si Planta es 0, empty() dice que es vacío. Usamos !== null
        if ($fila['Planta'] !== null && $fila['Planta'] !== '') {
            $plantas[] = $fila['Planta'];
        }
    }
}

// 5. Consultas para los desplegables del formulario (AULAS)
$sql_aulas = "SELECT DISTINCT Aula FROM Equipo ORDER BY Aula";
$res_aulas = $conn->query($sql_aulas);
$aulas = [];
if ($res_aulas) {
    while ($fila = $res_aulas->fetch_assoc()) {
        if (!empty($fila['Aula'])) {
            $aulas[] = $fila['Aula'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Equipos (Técnico)</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="page-app">

    <section class="dashboard-container">
        
        <aside class="sidebar">
            <header class="logopaginas"> <img src="IMGBLANCO/logo_completo_negativp.png" alt="CAMPUS DIGITAL"></header>
            
            <?php if ($rol_usuario_logueado == 'profesor'): ?>
            
                <a href="dashboard.php" class="<?php echo ($pagina_activa == 'dashboard') ? 'activo' : ''; ?>"><img src="IMGBLANCO/dashboard.png" alt="imagen de casa para el dashboard">Dashboard</a>
                <a href="profesorEquipos.php" class="<?php echo ($pagina_activa == 'equipos') ? 'activo' : ''; ?>"><img src="IMGBLANCO/equipo.png" alt="imagen de una pantalla de ordenador">Equipos</a>
                <a href="gestionAlumnos.php" class="<?php echo ($pagina_activa == 'alumnos') ? 'activo' : ''; ?>"><img src="IMGBLANCO/alumno.png" alt="icono de un alumno">Alumnos y profesores</a>
                <a href="gestionAulas.php" class="<?php echo ($pagina_activa == 'aulas') ? 'activo' : ''; ?>"><img src="IMGBLANCO/aula.png" alt="imagen de una mesa de clase">Aulas</a>
                <a href="profesorIncidencias.php" class="<?php echo ($pagina_activa == 'incidencias') ? 'activo' : ''; ?>"><img src="IMGBLANCO/incidencia.png" alt="icono de un globo con una exclamación en medio">Incidencias/Solicitudes</a>
            
            <?php elseif ($rol_usuario_logueado == 'tecnico'): ?>
            
                <a href="dashboard.php" class="<?php echo ($pagina_activa == 'dashboard') ? 'activo' : ''; ?>"><img src="IMGBLANCO/dashboard.png" alt="imagen de casa para el dashboard">Dashboard</a>
                <a href="tecnicoEquipos.php" class="<?php echo ($pagina_activa == 'equipos') ? 'activo' : ''; ?>"><img src="IMGBLANCO/equipo.png" alt="imagen de una pantalla de ordenador">Equipos</a>
                <a href="gestionAlumnos.php" class="<?php echo ($pagina_activa == 'alumnos') ? 'activo' : ''; ?>"><img src="IMGBLANCO/alumno.png" alt="icono de un alumno">Alumnos y profesores</a>
                <a href="gestionAulas.php" class="<?php echo ($pagina_activa == 'aulas') ? 'activo' : ''; ?>"><img src="IMGBLANCO/aula.png" alt="imagen de una mesa de clase">Aulas</a>
                <a href="tecnicoIncidencias.php" class="<?php echo ($pagina_activa == 'incidencias') ? 'activo' : ''; ?>"><img src="IMGBLANCO/incidencia.png" alt="icono de un globo con una exclamación en medio">Incidencias/Solicitudes</a>
                
            <?php else: // (Si no, es Alumno) ?>
            
                <a href="dashboard.php" class="<?php echo ($pagina_activa == 'dashboard') ? 'activo' : ''; ?>"><img src="IMGBLANCO/dashboard.png" alt="imagen de casa para el dashboard">Dashboard</a>
                <a href="misEquipos.php" class="<?php echo ($pagina_activa == 'equipos') ? 'activo' : ''; ?>"><img src="IMGBLANCO/equipo.png" alt="imagen de una pantalla de ordenador">Equipos</a>
                <a href="misIncidencias.php" class="<?php echo ($pagina_activa == 'historial') ? 'activo' : ''; ?>"><img src="IMGBLANCO/grafico-de-barras.png" alt="icono de grafica de columnas">Mi Historial</a>
                <a href="formularioTicket.php" class="<?php echo ($pagina_activa == 'incidencias') ? 'activo' : ''; ?>"><img src="IMGBLANCO/incidencia.png" alt="icono de un globo con una exclamación en medio">Incidencias/Solicitudes</a>
                
            <?php endif; ?>
            
            <a href="logout.php" class="logout">Cerrar Sesión</a>
        </aside> <main class="content-wrapper">

            <header class="top-header">
                <span class="header-title">
                    <h1>Gestión de Equipos</h1>
                </span>
                <span class="user-info">
                    <?php echo ucfirst($rol_usuario_logueado); ?>
                </span>
            </header>
            
            <section class="main-content">
                
                <?php echo $mensaje; ?>

                <details>
                    <summary>Añadir Nuevo Equipo</summary>
                    <article class="form-container">
                        <form action="guardarEquipo.php" method="POST">
                            <section class="form-grid">
                                <p>
                                    <label for="tipo">Tipo:</label>
                                    <input type="text" name="Tipo" id="tipo" placeholder="Ej: Portátil" required>
                                </p>
                                <p>
                                    <label for="num_equipo">Nº Equipo:</label>
                                    <input type="text" name="NumeroEquipo" id="num_equipo" placeholder="Ej: Portátil 1" required>
                                </p>
                                <p>
                                    <label for="num_serie">Nº Serie:</label>
                                    <input type="text" name="NumeroSerie" id="num_serie" placeholder="N/A si no tiene">
                                </p>
                                

                                <p class="form-group">
                                    <label for="planta">Planta:</label>
                                    <select name="Planta" id="planta" required>
                                        <option value="">-- Seleccionar Planta --</option>
                                        <?php
                                        // Reutilizamos el array $plantas que ya cargamos arriba
                                        foreach ($plantas as $planta_bd) {
                                            echo "<option value=\"" . htmlspecialchars($planta_bd) . "\">" . htmlspecialchars($planta_bd) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </p>
                            
                                <p class="form-group">
                                    <label for="aula">Aula:</label>
                                    <select name="Aula" id="aula" required>
                                        <option value="">-- Seleccionar Aula --</option>
                                        <?php
                                        // Reutilizamos el array $aulas que ya cargamos arriba
                                        foreach ($aulas as $aula_bd) {
                                            echo "<option value=\"" . htmlspecialchars($aula_bd) . "\">" . htmlspecialchars($aula_bd) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </p>
                                

                                <p>
                                    <label for="ubicacion">Ubicación:</label>
                                    <input type="text" name="Ubicacion" id="ubicacion" placeholder="Ej: Armario cargadores" required>
                                </p>

                                <p>
                                    <label for="estado">Estado:</label>
                                    <select name="Estado" id="estado" required>
                                        <option value="Bien">Bien</option>
                                        <option value="Roto">Roto</option>
                                        <option value="En reparación">En reparación</option>
                                        <option value="En espera">En espera</option>
                                    </select>
                                </p>
                                <p class="full-width">
                                    <label for="id_usuario_asignado">Asignar a:</label>
                                    <select name="IdUsuarioAsignado" id="id_usuario_asignado">
                                        <option value="">-- Sin Asignar --</option>
                                        <?php
                                        while ($usuario = $resultado_usuarios->fetch_assoc()) {
                                            echo "<option value='" . $usuario['IdUsuario'] . "'>";
                                            echo htmlspecialchars($usuario['Nombre'] . ' ' . $usuario['Apellidos'] . " (" . $usuario['Rol'] . ")");
                                            echo "</option>";
                                        }
                                        ?>
                                    </select>
                                </p>
                            </section> <button type="submit">Guardar Equipo</button>
                        </form>
                    </article> </details>
                
                
                <h2>Listado de Equipos</h2>
                <?php
                $sql_equipos = "SELECT 
                                    e.Planta, e.Aula, e.Tipo, e.NumeroEquipo, e.Estado, e.IdUsuarioAsignado,
                                    u.Nombre, 
                                    u.Apellidos 
                                FROM Equipo AS e
                                LEFT JOIN Usuario AS u ON e.IdUsuarioAsignado = u.IdUsuario
                                ORDER BY " . $sql_orden;
                                
                $resultado_equipos_tabla = $conn->query($sql_equipos);
                
                echo "<p><strong>Total de equipos: " . $resultado_equipos_tabla->num_rows . "</strong></p>";

                echo "<table>";
                echo "<tr>
                    <th>Nº</th>
                    <th>Tipo</th>
                    <th>Nº Equipo</th>
                    
                    <th>
                        <a href='?col=planta&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>
                            Planta" . flecha('planta', $columna, $direccion) . "
                        </a>
                    </th>
                    
                    <th>
                        <a href='?col=aula&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>
                            Aula" . flecha('aula', $columna, $direccion) . "
                        </a>
                    </th>
                    
                    <th>Estado</th>
                    
                    <th>
                        <a href='?col=asignado&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>
                            Asignado a" . flecha('asignado', $columna, $direccion) . "
                        </a>
                    </th>
                </tr>";
                
                $contador = 1;
                while ($equipo = $resultado_equipos_tabla->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $contador . "</td>";
                    echo "<td>" . $equipo['Tipo'] . "</td>";
                    echo "<td>" . $equipo['NumeroEquipo'] . "</td>";
                    echo "<td>" . $equipo['Planta'] . "</td>";
                    echo "<td>" . $equipo['Aula'] . "</td>";
                    echo "<td>" . $equipo['Estado'] . "</td>";
                    
                    if ($equipo['Nombre']) {
                        echo "<td>" . htmlspecialchars($equipo['Nombre'] . ' ' . $equipo['Apellidos']) . "</td>";
                    } else {
                        echo "<td><i>Sin asignar</i></td>";
                    }
                    
                    echo "</tr>";
                    $contador++;
                }
                echo "</table>";
                
                $conn->close();
                ?>
            </section> </main> </section> </body>
</html>