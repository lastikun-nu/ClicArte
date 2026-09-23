<?php
// verTicket.php
session_start();
require 'conexion.php';

// 1. Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}
$id_usuario_logueado = $_SESSION['id_usuario'];
$rol_usuario_logueado = strtolower($_SESSION['rol']);

// 2. Validar ID de ticket
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: No se ha especificado un ID de ticket válido.");
}
$ticket_id = $_GET['id'];

// 3. CONSULTA A LA BD 
$sql = "SELECT 
            t.*, 
            u_creador.Nombre AS CreadorNombre, u_creador.Apellidos AS CreadorApellidos,
            u_tecnico.Nombre AS TecnicoNombre, u_tecnico.Apellidos AS TecnicoApellidos,
            e.Tipo AS EquipoTipo, e.Ubicacion AS EquipoUbicacion, e.NumeroEquipo, e.Planta, e.Aula
        FROM Ticket t
        JOIN Usuario u_creador ON t.IdUsuario = u_creador.IdUsuario
        JOIN Equipo e ON t.IdEquipo = e.IdEquipo
        LEFT JOIN Usuario AS u_tecnico ON t.IdTecnicoAsignado = u_tecnico.IdUsuario
        WHERE t.IdTicket = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Ticket no encontrado.");
}
$ticket = $resultado->fetch_assoc();
$stmt->close();

// 4. Verificación de Seguridad (A Nivel de Fila)
if ($rol_usuario_logueado != 'profesor') {
    if ($ticket['IdUsuario'] != $id_usuario_logueado && $ticket['IdTecnicoAsignado'] != $id_usuario_logueado) {
        die("Acceso denegado. No tienes permiso para ver este ticket.");
    }
}

// Determinar $pagina_activa
if ($rol_usuario_logueado == 'usuario') {
    $pagina_activa = 'historial';
} else {
    $pagina_activa = 'incidencias';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detalle del Ticket</title>
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
            
            <header class="main-header">
                <h1>Gestión de Incidencias/Solicitudes</h1>
            </header>
            
            <section class="main-content">

                <?php
                // Lógica INTELIGENTE del botón volver
                $origen = isset($_GET['origen']) ? $_GET['origen'] : '';
                
                switch ($origen) {
                    case 'mis':
                        $volver_url = "misIncidencias.php";
                        $texto_volver = "Volver a Mis Incidencias";
                        break;
                    case 'profe':
                        $volver_url = "profesorIncidencias.php";
                        $texto_volver = "Volver al Listado General";
                        break;
                    case 'tecnico':
                        $volver_url = "tecnicoIncidencias.php";
                        $texto_volver = "Volver a Gestión";
                        break;
                    default:
                        // Si no hay origen (fallback), usamos la lógica del rol
                        if ($rol_usuario_logueado == 'tecnico') {
                            $volver_url = "tecnicoIncidencias.php";
                        } elseif ($rol_usuario_logueado == 'profesor') {
                            $volver_url = "profesorIncidencias.php";
                        } else {
                            $volver_url = "misIncidencias.php";
                        }
                        $texto_volver = "Volver al Listado";
                        break;
                }
                ?>
                
                <article class="widget form-container">
                    
                    <div style="text-align: left; margin-bottom: 20px;">
                        <a href="<?php echo $volver_url; ?>" class="btn-volver">&larr; Volver al Listado</a>
                    </div>
                    
                    <h2 style="text-align: center; color: #333; margin-top: 0;">Detalle del Ticket (Nº: <?php echo $ticket['IdTicket']; ?>)</h2>

                    <h2>Detalles de la Solicitud</h2>
                    <section class="form-grid-2-col">
                        <p class="form-group">
                            <label>Creado por:</label>
                            <strong><?php echo htmlspecialchars($ticket['CreadorNombre'] . ' ' . $ticket['CreadorApellidos']); ?></strong>
                        </p>
                        <p class="form-group">
                            <label>Equipo:</label>
                            <strong><?php echo htmlspecialchars($ticket['EquipoTipo'] . ' (Nº ' . $ticket['NumeroEquipo'] . ')'); ?></strong>
                        </p>
                        <p class="form-group" style="grid-column: span 2;">
                            <label>Ubicación:</label>
                            <strong><?php echo "Planta " . htmlspecialchars($ticket['Planta']) . " - " . htmlspecialchars($ticket['Aula']) . " (" . htmlspecialchars($ticket['EquipoUbicacion']) . ")"; ?></strong>
                        </p>
                    </section>
                    
                    <p class="form-group full-width">
                        <label>Descripción del Usuario:</label>
                    </p>
                    
                    <blockquote class="ticket-description" style="margin-top: 0;">
                        <?php echo nl2br(htmlspecialchars($ticket['Descripcion'])); ?>
                    </blockquote>
                    
                    <hr style="border: none; border-top: 2px solid #f0ad4e; margin: 25px 0 10px 0;">
                    
                    <h2>Estado de la Resolución</h2>
                    <section class="form-grid-2-col">
                        <p class="form-group">
                            <label>Estado del Ticket:</label>
                            <strong><?php echo htmlspecialchars(ucfirst($ticket['Estado'])); ?></strong>
                        </p>
                        <p class="form-group">
                            <label>Técnico Asignado:</label>
                            <strong><?php echo $ticket['TecnicoNombre'] ? htmlspecialchars($ticket['TecnicoNombre'] . ' ' . $ticket['TecnicoApellidos']) : '<i>Sin asignar</i>'; ?></strong>
                        </p>
                         <p class="form-group">
                            <label>Fecha de inicio:</label>
                            <strong><?php echo htmlspecialchars($ticket['FechaInicio']); ?></strong>
                        </p>
                        <p class="form-group">
                            <label>Fecha de fin:</label>
                            <strong><?php echo $ticket['FechaFin'] ? htmlspecialchars($ticket['FechaFin']) : '---'; ?></strong>
                        </p>
                    </section>
                    
                    <p class="form-group full-width">
                        <label>Comentarios / Resolución del Técnico:</label>
                    </p>
                    <blockquote class="ticket-comment">
                        <?php
                        echo !empty($ticket['Comentario'])
                            ? nl2br(htmlspecialchars($ticket['Comentario']))
                            : '<i>Aún no hay comentarios o notas de resolución por parte del técnico.</i>';
                        ?>
                    </blockquote>

                </article>

            </section> 
        </main> 
    </section> 
</body>
</html>