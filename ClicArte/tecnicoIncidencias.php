<?php
// tecnicoIncidencias.php
session_start();
$pagina_activa = 'incidencias'; // Para el menú
require 'conexion.php';

// 1. Seguridad: Proteger la página
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || $rol_usuario_logueado != 'tecnico') {
    header("Location: index.php?error=Acceso denegado");
    exit;
}
$id_usuario_logueado = $_SESSION['id_usuario'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Incidencias (Técnico)</title>
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
                    <h1>Gestión de Incidencias</h1>
                </span>
                <span class="user-info">
                    <?php echo ucfirst($rol_usuario_logueado); ?>
                </span>
            </header>
            
            <section class="main-content">
                
                <p style="margin-bottom: 20px;">
                    <a href="formularioTicket.php" class="button">Crear Nueva Incidencia/Solicitud</a>
                    <span style="display: block; margin-top: 10px; color: #666;">Aquí puedes gestionar todas las incidencias y solicitudes del centro.</span>
                </p>
                
                <?php
                $sql_incidencias = "SELECT 
                                        t.IdTicket, t.Tipo, t.Estado, t.FechaInicio,
                                        u.Nombre, u.Apellidos
                                      FROM Ticket t
                                      JOIN Usuario u ON t.IdUsuario = u.IdUsuario
                                      ORDER BY t.FechaInicio ASC";
                                    
                $resultado_incidencias = $conn->query($sql_incidencias);
                
                echo "<table>";
                echo "<tr>
                        <th>Nº</th>
                        <th>Estado</th>
                        <th>Tipo</th>
                        <th>Creado por</th>
                        <th>Fecha Creación</th>
                        <th>Acción</th>
                      </tr>";
                
                $contador = 1;
                while ($ticket = $resultado_incidencias->fetch_assoc()) {
                    // Lógica de Colores (Badges)
                    $clase_estado = 'bg-rojo';
                    if (strtolower($ticket['Estado']) == 'finalizado') $clase_estado = 'bg-finalizado';
                    elseif (strtolower($ticket['Estado']) == 'en espera') $clase_estado = 'bg-espera';
                    
                    echo "<tr>";
                    echo "<td>" . $contador . "</td>";
                    
                    // Estado con la etiqueta de color
                    echo "<td><span class='estado-badge " . $clase_estado . "'>" . ucfirst($ticket['Estado']) . "</span></td>";
                    
                    echo "<td>" . ucfirst($ticket['Tipo']) . "</td>";
                    echo "<td>" . htmlspecialchars($ticket['Nombre'] . ' ' . $ticket['Apellidos']) . "</td>";
                    echo "<td>" . date('d/m/Y H:i', strtotime($ticket['FechaInicio'])) . "</td>";
                    
                    // Enlace con origen=tecnico para que el botón volver funcione
                    echo "<td><a href='editarTicket.php?id=" . $ticket['IdTicket'] . "&origen=tecnico' class='btn-gestionar'>Gestionar</a></td>";
                    
                    echo "</tr>";
                    $contador++;
                }
                echo "</table>";
                
                $conn->close();
                ?>
            </section> </main> </section> </body>
</html>