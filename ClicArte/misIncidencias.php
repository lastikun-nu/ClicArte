<?php
// misIncidencias.php
session_start();
$pagina_activa = 'historial'; // Para el menú
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
    <title>Mi Historial de Incidencias</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="page-app">

    <!-- Contenedor principal -->
    <section class="dashboard-container">
        
        <!-- Barra lateral -->
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
        </aside> <!-- Fin .sidebar -->
        
        <!-- Contenedor derecho -->
        <main class="content-wrapper">

            <header class="top-header">
                <span class="header-title">
                    <h1>Historial de Incidencias</h1>
                </span>
                <span class="user-info">
                    <?php echo ucfirst($rol_usuario_logueado); ?>
                </span>
            </header>
            
            <!-- Contenido principal -->
            <section class="main-content">
                
                <!-- Contenedor del botón -->
                <p style="margin-bottom: 25px;">
                    <a href="formularioTicket.php" class="button">Crear Nueva Incidencia</a>
                </p>
                
                <?php
                $sql_usuario = "SELECT IdTicket, Tipo, Estado, FechaInicio
                                FROM Ticket
                                WHERE IdUsuario = ?
                                ORDER BY FechaInicio ASC";
                
                $stmt = $conn->prepare($sql_usuario);
                $stmt->bind_param("i", $id_usuario_logueado); 
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    echo "<table>";
                    echo "<tr>
                            <th>Nº</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acción</th>
                          </tr>";
                    
                    $contador = 1;
                    while ($ticket = $resultado->fetch_assoc()) {
                        
                        // Lógica para el color del badge
                        $clase_estado = 'bg-rojo'; // Por defecto (pendiente/abierto)
                        if ($ticket['Estado'] == 'finalizado') $clase_estado = 'bg-finalizado';
                        elseif ($ticket['Estado'] == 'en espera') $clase_estado = 'bg-espera';

                        echo "<tr>";
                        echo "<td>" . $contador . "</td>";
                        echo "<td>" . ucfirst($ticket['Tipo']) . "</td>";
                        echo "<td><span class='estado-badge " . $clase_estado . "'>" . ucfirst($ticket['Estado']) . "</span></td>";
                        echo "<td>" . $ticket['FechaInicio'] . "</td>";
                        echo "<td><a href='verTicket.php?id=" . $ticket['IdTicket'] . "&origen=mis' class='btn-consultar'>Consultar</a></td>";
                        echo "</tr>";
                        $contador++;
                    }
                    echo "</table>";
                } else {
                    echo "<p class='empty-state' style='margin-top: 20px;'>Aún no has registrado ninguna incidencia.</p>";
                }
                
                $stmt->close();
                $conn->close();
                ?>
            </section> <!-- Fin .main-content -->
        </main> <!-- Fin .content-wrapper -->
        
    </section> <!-- Fin .dashboard-container -->

</body>
</html>