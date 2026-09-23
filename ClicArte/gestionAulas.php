<?php
// gestionAulas.php
session_start();
$pagina_activa = 'aulas'; // Para el menú
require 'conexion.php';

// 1. Seguridad: Proteger la página
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || ($rol_usuario_logueado != 'profesor' && $rol_usuario_logueado != 'tecnico')) {
    header("Location: index.php?error=Acceso denegado");
    exit;
}
$id_usuario_logueado = $_SESSION['id_usuario'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Aulas</title>
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
                    <h1>Gestión de Aulas</h1>
                </span>
                <span class="user-info">
                    <?php echo ucfirst($rol_usuario_logueado); ?>
                </span>
            </header>
            
            <section class="main-content">
                
                <p>Listado de equipos y alumnos asignados a cada aula.</p>
                
                <?php
                // 1. Obtener la lista de Aulas únicas de la tabla Equipo
                $sql_aulas = "SELECT DISTINCT Aula, Planta FROM Equipo WHERE Aula IS NOT NULL AND Aula != '' ORDER BY Planta, Aula ASC";
                $resultado_aulas = $conn->query($sql_aulas);
                
                if ($resultado_aulas->num_rows == 0) {
                    echo "<p>No hay aulas registradas con equipos.</p>";
                }

                // 2. Por cada Aula, buscar sus equipos y alumnos
                while ($aula = $resultado_aulas->fetch_assoc()) {
                    $aula_nombre = $aula['Aula'];
                    $aula_planta = $aula['Planta'];
                ?>
                
                    <article class="aula-container">
                        <header class="aula-header">
                            <h2>
                                <?php echo "(Planta " . htmlspecialchars($aula_planta) . ") - " . htmlspecialchars($aula_nombre); ?>
                            </h2>
                        </header>
                        
                        <section class="aula-body">
                            
                            <section class="aula-columna">
                                <h3>Equipos en esta aula</h3>
                                <?php
                                $sql_equipos = "SELECT Tipo, NumeroEquipo, Estado FROM Equipo WHERE Aula = ? ORDER BY Tipo, NumeroEquipo";
                                $stmt_equipos = $conn->prepare($sql_equipos);
                                $stmt_equipos->bind_param("s", $aula_nombre);
                                $stmt_equipos->execute();
                                $resultado_equipos = $stmt_equipos->get_result();
                                
                                if ($resultado_equipos->num_rows > 0) {
                                    echo "<ul>";
                                    while ($equipo = $resultado_equipos->fetch_assoc()) {
                                        echo "<li>" . htmlspecialchars($equipo['Tipo'] . ' - ' . $equipo['NumeroEquipo']) . " (" . htmlspecialchars($equipo['Estado']) . ")</li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "<p>No hay equipos registrados en esta aula.</p>";
                                }
                                $stmt_equipos->close();
                                ?>
                            </section> <section class="aula-columna">
                                <h3>Alumnos asignados a esta aula</h3>
                                <?php
                                // Buscamos alumnos (Rol='Usuario') que tengan un equipo asignado (IdUsuarioAsignado) que esté en esta aula
                                $sql_alumnos = "SELECT DISTINCT u.Nombre, u.Apellidos 
                                                FROM Usuario u
                                                JOIN Equipo e ON u.IdUsuario = e.IdUsuarioAsignado
                                                WHERE e.Aula = ? AND u.Rol = 'Usuario'
                                                ORDER BY u.Apellidos, u.Nombre";
                                $stmt_alumnos = $conn->prepare($sql_alumnos);
                                $stmt_alumnos->bind_param("s", $aula_nombre);
                                $stmt_alumnos->execute();
                                $resultado_alumnos = $stmt_alumnos->get_result();
                                
                                if ($resultado_alumnos->num_rows > 0) {
                                    echo "<ul>";
                                    while ($alumno = $resultado_alumnos->fetch_assoc()) {
                                        echo "<li>" . htmlspecialchars($alumno['Nombre'] . ' ' . $alumno['Apellidos']) . "</li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "<p>No hay alumnos con equipos asignados en esta aula.</p>";
                                }
                                $stmt_alumnos->close();
                                ?>
                            </section> </section> </article> <?php
                } // Fin del bucle de Aulas
                $conn->close();
                ?>
                
            </section> </main> </section> </body>
</html>