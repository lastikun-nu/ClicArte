<?php
// dashboard.php
session_start();
$pagina_activa = 'dashboard'; // Para el menú
require 'conexion.php';

// 1. Seguridad: Proteger la página
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || ($rol_usuario_logueado != 'usuario' && $rol_usuario_logueado != 'profesor' && $rol_usuario_logueado != 'tecnico')) {
    header("Location: index.php?error=Acceso denegado");
    exit;
}
$id_usuario_logueado = $_SESSION['id_usuario'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
        </aside> 
        <main class="content-wrapper">

            <header class="top-header">
                <span class="header-title">
                    <h1>Gestión de Aulas</h1> 
                </span>
            </header>
            
            <section class="main-content">
            
                <?php
                // --- LÓGICA DE CONSULTAS COMUNES ---
                
                // 1. OBTENER DATOS DEL USUARIO
                $sql_usuario = "SELECT Nombre FROM Usuario WHERE IdUsuario = ?";
                $stmt_usuario = $conn->prepare($sql_usuario);
                $stmt_usuario->bind_param("i", $id_usuario_logueado);
                $stmt_usuario->execute();
                $resultado_usuario = $stmt_usuario->get_result();
                $usuario = $resultado_usuario->fetch_assoc();
                $nombre_usuario = $usuario['Nombre'];
                $stmt_usuario->close();

                // 2. OBTENER DATOS DE "MI ORDENADOR ASIGNADO"
                $sql_equipo = "SELECT Tipo, Aula, NumeroEquipo, Estado, Planta
                               FROM Equipo 
                               WHERE IdUsuarioAsignado = ? 
                               ORDER BY (Tipo = 'Portátil') DESC, IdEquipo ASC 
                               LIMIT 1"; 
                $stmt_equipo = $conn->prepare($sql_equipo);
                $stmt_equipo->bind_param("i", $id_usuario_logueado);
                $stmt_equipo->execute();
                $resultado_equipo = $stmt_equipo->get_result();
                $stmt_equipo->close(); 

                // 3. OBTENER DATOS DE "MIS INCIDENCIAS" (Personales)
                $sql_incidencias = "SELECT COUNT(*) AS total_abiertas 
                                    FROM Ticket 
                                    WHERE IdUsuario = ? 
                                    AND (Estado != 'finalizado' AND Estado != 'resuelto')";
                $stmt_incidencias = $conn->prepare($sql_incidencias);
                $stmt_incidencias->bind_param("i", $id_usuario_logueado);
                $stmt_incidencias->execute();
                $resultado_incidencias = $stmt_incidencias->get_result();
                $datos_incidencias = $resultado_incidencias->fetch_assoc();
                $total_incidencias_abiertas = $datos_incidencias['total_abiertas'];
                $stmt_incidencias->close();
                ?>

                <h1>Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
                
                <section class="dashboard-widgets">
                
                    <article class="widget">
                        <h3>Mi ordenador asignado</h3>
                        <?php
                        if ($resultado_equipo->num_rows > 0) {
                            $equipo = $resultado_equipo->fetch_assoc();
                            echo "<p><strong>Equipo:</strong> " . htmlspecialchars($equipo['Tipo']) . " (Nº Equipo " . htmlspecialchars($equipo['NumeroEquipo']) . ")</p>";
                            echo "<p><strong>Planta:</strong> " . htmlspecialchars($equipo['Planta']) . "</p>";
                            echo "<p><strong>Ubicación:</strong> " . htmlspecialchars($equipo['Aula']) . "</p>";
                            echo "<hr>";
                            echo "<p><strong>Estado del equipo:</strong> " . htmlspecialchars($equipo['Estado']) . "</p>";
                        } else {
                            echo "<p>Actualmente no tienes ningún equipo principal asignado.</p>";
                        }
                        ?>
                    </article>

                    <article class="widget">
                        <h3>Mis Incidencias</h3>
                        <?php
                        if ($total_incidencias_abiertas == 1) {
                            echo "<p>Actualmente tienes <strong>1 incidencia</strong> personal abierta.</p>";
                        } elseif ($total_incidencias_abiertas > 1) {
                            echo "<p>Actualmente tienes <strong>" . $total_incidencias_abiertas . "</strong> incidencias personales abiertas.</p>";
                        } else {
                            echo "<p>¡Genial! No tienes ninguna incidencia personal en curso.</p>";
                        }
                        ?>
                        <hr>
                        <a href="misIncidencias.php" class="button">Ver Mi Historial</a>
                        <a href="formularioTicket.php" class="button">Crear Nueva</a>
                    </article>

                    <?php
                    // -----------------------------------------------------------------
                    // WIDGETS EXTRA DEL PROFESOR
                    // -----------------------------------------------------------------
                    if ($rol_usuario_logueado == 'profesor') {
                        
                        $sql_op = "SELECT COUNT(*) AS total_op FROM Equipo WHERE Estado = 'Bien'";
                        $res_op = $conn->query($sql_op);
                        $total_operativos = $res_op->fetch_assoc()['total_op'];
                        
                        $sql_rep = "SELECT COUNT(*) AS total_rep FROM Equipo WHERE Estado != 'Bien'";
                        $res_rep = $conn->query($sql_rep);
                        $total_reparacion = $res_rep->fetch_assoc()['total_rep'];
                        ?>
                        
                        <article class="widget">
                            <h3>Estado General del Centro</h3>
                            <p>Un resumen del estado de todos los equipos en el sistema.</p>
                            <hr>
                            <p><strong>Equipos operativos:</strong> <?php echo $total_operativos; ?></p>
                            <p><strong>Equipos con incidencias:</strong> <?php echo $total_reparacion; ?></p>
                        </article>

                        <?php
                    } // Fin del if ($rol_usuario_logueado == 'profesor')
                    ?>
                    
                    <?php
                    // -----------------------------------------------------------------
                    // WIDGETS EXTRA DEL TÉCNICO
                    // -----------------------------------------------------------------
                    if ($rol_usuario_logueado == 'tecnico') {
                        
                        // WIDGET 3 (TÉCNICO): ESTADO DE TICKETS
                        $sql_op = "SELECT COUNT(*) AS total FROM Ticket WHERE Estado = 'en curso'";
                        $res_op = $conn->query($sql_op);
                        $total_en_curso = $res_op->fetch_assoc()['total'];
                        
                        $sql_rep = "SELECT COUNT(*) AS total FROM Ticket WHERE Estado = 'en espera'";
                        $res_rep = $conn->query($sql_rep);
                        $total_en_espera = $res_rep->fetch_assoc()['total']; 
                        ?>
                        
                        <article class="widget">
                            <h3>Estado de Tickets</h3>
                            <p>Un resumen de todos los tickets abiertos en el sistema.</p>
                            <hr>
                            <p><strong>Tickets 'En Curso':</strong> <?php echo $total_en_curso; ?></p>
                            <p><strong>Tickets 'En Espera':</strong> <?php echo $total_en_espera; ?></p>
                        </article>

                        <?php
                        // WIDGET 4 (TÉCNICO): ESTADO GENERAL
                        $sql_op_eq = "SELECT COUNT(*) AS total_op FROM Equipo WHERE Estado = 'Bien'";
                        $res_op_eq = $conn->query($sql_op_eq);
                        $total_operativos_eq = $res_op_eq->fetch_assoc()['total_op'];
                        
                        $sql_rep_eq = "SELECT COUNT(*) AS total_rep FROM Equipo WHERE Estado != 'Bien'";
                        $res_rep_eq = $conn->query($sql_rep_eq);
                        $total_reparacion_eq = $res_rep_eq->fetch_assoc()['total_rep'];
                        ?>
                        
                        <article class="widget" style="text-align: left;">
                            <h3>Estado General del Centro</h3>
                            <p>Un resumen del estado de todos los equipos en el sistema.</p>
                            <hr>
                            <p><strong>Equipos operativos:</strong> <?php echo $total_operativos_eq; ?></p>
                            <p><strong>Equipos con incidencias:</strong> <?php echo $total_reparacion_eq; ?></p>
                        </article>

                        <?php
                    } // Fin del if ($rol_usuario_logueado == 'tecnico')
                    ?>
                    
                </section> <?php $conn->close(); ?>
                
            </section> </main> </section> </body>
</html>