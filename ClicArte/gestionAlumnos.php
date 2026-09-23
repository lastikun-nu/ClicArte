<?php
// gestionAlumnos.php
session_start();
$pagina_activa = 'alumnos'; // Para el menú
require 'conexion.php';

// 1. Seguridad: Proteger la página
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || ($rol_usuario_logueado != 'profesor' && $rol_usuario_logueado != 'tecnico')) {
    header("Location: index.php?error=Acceso denegado");
    exit;
}
$id_usuario_logueado = $_SESSION['id_usuario'];


// --- LÓGICA DE ORDENACIÓN ---
$columna = isset($_GET['col']) ? $_GET['col'] : 'apellidos'; // Por defecto: Apellidos
$direccion = isset($_GET['dir']) && $_GET['dir'] == 'DESC' ? 'DESC' : 'ASC';
$nueva_dir = ($direccion == 'ASC') ? 'DESC' : 'ASC'; // Invierte para el próximo clic

// Definimos el SQL según la columna clicada
switch ($columna) {
    case 'nombre':
        $sql_orden = "Nombre $direccion, Apellidos ASC";
        break;
    case 'apellidos':
        $sql_orden = "Apellidos $direccion, Nombre ASC";
        break;
    case 'curso':
        $sql_orden = "Curso $direccion, Apellidos ASC";
        break;
    case 'especialidad':
        $sql_orden = "Especialidad $direccion, Apellidos ASC";
        break;
    case 'turno':
        $sql_orden = "Turno $direccion, Apellidos ASC";
        break;
    default:
        $sql_orden = "Apellidos ASC, Nombre ASC";
        break;
}

// Función auxiliar flecha
function flecha($col_actual, $col_seleccionada, $dir) {
    if ($col_actual == $col_seleccionada) {
        return ($dir == 'ASC') ? ' ▲' : ' ▼';
    }
    return '';
}



// 2. Lógica para mostrar mensajes de éxito/error
$mensaje = '';
if (isset($_GET['success'])) {
    $mensaje = "<p class='mensaje success'>Usuario añadido correctamente.</p>";
}
if (isset($_GET['error'])) {
    $mensaje = "<p class='mensaje error'>" . htmlspecialchars($_GET['error']) . "</p>";
}



// --- CONSULTAS PARA LOS DESPLEGABLES ---
// 1. Obtener CURSOS existentes
$sql_cursos = "SELECT DISTINCT Curso FROM Usuario WHERE Curso IS NOT NULL ORDER BY Curso";
$res_cursos = $conn->query($sql_cursos);
$cursos = [];
if ($res_cursos) {
    while ($row = $res_cursos->fetch_assoc()) {
        $cursos[] = $row['Curso'];
    }
}

// 2. Obtener ESPECIALIDADES existentes
$sql_esp = "SELECT DISTINCT Especialidad FROM Usuario WHERE Especialidad IS NOT NULL AND Especialidad != '' ORDER BY Especialidad";
$res_esp = $conn->query($sql_esp);
$especialidades = [];
if ($res_esp) {
    while ($row = $res_esp->fetch_assoc()) {
        $especialidades[] = $row['Especialidad'];
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alumnos</title>
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
                    <h1>Gestión de Alumnos</h1>
                </span>
                <span class="user-info">
                    <?php echo ucfirst($rol_usuario_logueado); ?>
                </span>
            </header>
            
            <section class="main-content">
                
                <?php echo $mensaje; // Muestra mensajes de éxito/error ?>

                <details>
                    <summary>Añadir Nuevo Usuario (Alumno/Profesor)</summary>
                    <article class="form-container">
                        <form action="guardarUsuario.php" method="POST">
                            <section class="form-grid">
                                <p>
                                    <label for="nombre">Nombre:</label>
                                    <input type="text" name="Nombre" id="nombre" required>
                                </p>
                                <p>
                                    <label for="apellidos">Apellidos:</label>
                                    <input type="text" name="Apellidos" id="apellidos" required>
                                </p>
                                
                                <p class="full-width">
                                    <label for="correo">Correo:</label>
                                    <input type="email" name="Correo" id="correo" required>
                                </p>
                                
                                <p>
                                    <label for="contrasena">Contraseña:</label>
                                    <input type="password" name="Contrasena" id="contrasena" required>
                                </p>
                                <p>
                                    <label for="rol">Rol:</label>
                                    <select name="Rol" id="rol" required>
                                        <option value="Usuario">Usuario (Alumno)</option>
                                        <option value="Profesor">Profesor</option>
                                    </select>
                                </p>
                                
                                <p class="form-group">
                                    <label for="curso">Curso:</label>
                                    <select name="Curso" id="curso" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php
                                        foreach ($cursos as $c) {
                                            echo "<option value='" . htmlspecialchars($c) . "'>" . htmlspecialchars($c) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </p>
                            
                                <p class="form-group">
                                    <label for="especialidad">Especialidad:</label>
                                    <select name="Especialidad" id="especialidad" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php
                                        foreach ($especialidades as $e) {
                                            echo "<option value=\"" . htmlspecialchars($e) . "\">" . htmlspecialchars($e) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </p>


                                <p>
                                    <label for="turno">Turno:</label>
                                    <select name="Turno" id="turno" required>
                                        <option value="M">Mañana</option>
                                        <option value="T">Tarde</option>
                                    </select>
                                </p>
                            </section> <button type="submit">Guardar Usuario</button>
                        </form>
                    </article> </details>
                
                <h2>Listado de Profesores</h2>
               <?php
                // 1. Consulta de Profesores con ORDEN DINÁMICO
                $sql_profesores = "SELECT Nombre, Apellidos, Correo, Curso, Especialidad, Turno 
                                FROM Usuario 
                                WHERE Rol = 'Profesor' 
                                ORDER BY " . $sql_orden;
                                    
                $resultado_profesores = $conn->query($sql_profesores);
                
                echo "<p><strong>Total de profesores: " . $resultado_profesores->num_rows . "</strong></p>";
                
                echo "<table>";
                echo "<tr>
                        <th>Nº</th>
                        
                        <th><a href='?col=nombre&dir=$nueva_dir' style='color: inherit;; text-decoration: underline; cursor: pointer;'>Nombre" . flecha('nombre', $columna, $direccion) . "</a></th>
                        
                        <th><a href='?col=apellidos&dir=$nueva_dir' style='color: inherit;; text-decoration: underline; cursor: pointer;'>Apellidos" . flecha('apellidos', $columna, $direccion) . "</a></th>
                        
                        <th>Correo</th>
                        
                        <th><a href='?col=curso&dir=$nueva_dir' style='color: inherit;; text-decoration: underline; cursor: pointer;'>Curso" . flecha('curso', $columna, $direccion) . "</a></th>
                        
                        <th><a href='?col=especialidad&dir=$nueva_dir' style='color: inherit;; text-decoration: underline; cursor: pointer;'>Especialidad" . flecha('especialidad', $columna, $direccion) . "</a></th>
                        
                        <th><a href='?col=turno&dir=$nueva_dir' style='color: inherit;; text-decoration: underline; cursor: pointer;'>Turno" . flecha('turno', $columna, $direccion) . "</a></th>
                    </tr>";
                
                $cont_prof = 1;
                while ($profe = $resultado_profesores->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $cont_prof . "</td>";
                    echo "<td>" . htmlspecialchars($profe['Nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($profe['Apellidos']) . "</td>";
                    echo "<td>" . htmlspecialchars($profe['Correo']) . "</td>";
                    echo "<td>" . ($profe['Curso'] ? htmlspecialchars($profe['Curso']) : '-') . "</td>";
                    echo "<td>" . ($profe['Especialidad'] ? htmlspecialchars($profe['Especialidad']) : '-') . "</td>";
                    echo "<td>" . ($profe['Turno'] ? htmlspecialchars($profe['Turno']) : '-') . "</td>";
                    echo "</tr>";
                    $cont_prof++;
                }
                echo "</table>";
            ?>
            
                <br><hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;"><br>


                <h2>Listado de Alumnos</h2>
                <?php
                // 1. Consulta de Alumnos con ORDEN DINÁMICO
                $sql_alumnos = "SELECT IdUsuario, Nombre, Apellidos, Correo, Curso, Especialidad, Turno
                                FROM Usuario 
                                WHERE Rol = 'Usuario' 
                                ORDER BY " . $sql_orden;
                                    
                $resultado_alumnos = $conn->query($sql_alumnos);
                
                echo "<p><strong>Total de alumnos: " . $resultado_alumnos->num_rows . "</strong></p>";
                
                echo "<table>";
                echo "<tr>
                        <th>Nº</th>
                        
                        <th><a href='?col=nombre&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>Nombre" . flecha('nombre', $columna, $direccion) . "</a></th>
                        
                        <th><a href='?col=apellidos&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>Apellidos" . flecha('apellidos', $columna, $direccion) . "</a></th>
                        
                        <th>Correo</th>
                        
                        <th><a href='?col=curso&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>Curso" . flecha('curso', $columna, $direccion) . "</a></th>
                        
                        <th><a href='?col=especialidad&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>Especialidad" . flecha('especialidad', $columna, $direccion) . "</a></th>
                        
                        <th><a href='?col=turno&dir=$nueva_dir' style='color: inherit; text-decoration: underline; cursor: pointer;'>Turno" . flecha('turno', $columna, $direccion) . "</a></th>
                    </tr>";
                
                $contador = 1;
                while ($alumno = $resultado_alumnos->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $contador . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['Nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['Apellidos']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['Correo']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['Curso']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['Especialidad']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['Turno']) . "</td>";
                    echo "</tr>";
                    $contador++;
                }
                echo "</table>";
                
                $conn->close();
                ?>
            </section> </main> </section> </body>
</html>