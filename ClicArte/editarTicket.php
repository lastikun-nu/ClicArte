<?php
// editarTicket.php
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

// 2. Obtener ID del ticket de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID de ticket no válido.");
}
$ticket_id = $_GET['id'];

// 3. Obtener datos del ticket
$sql = "SELECT t.*, u.Nombre, u.Apellidos, e.NumeroEquipo 
        FROM Ticket t
        JOIN Usuario u ON t.IdUsuario = u.IdUsuario
        JOIN Equipo e ON t.IdEquipo = e.IdEquipo
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

// 4. Obtener lista de todos los técnicos para el desplegable
$sql_tecnicos = "SELECT IdUsuario, Nombre, Apellidos FROM Usuario WHERE Rol = 'Tecnico'";
$resultado_tecnicos = $conn->query($sql_tecnicos);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestionar Ticket #<?php echo $ticket['IdTicket']; ?></title>
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
                <?php
                    // Calculamos a dónde debe volver el usuario
                    $origen = isset($_GET['origen']) ? $_GET['origen'] : '';
                    // Si viene de 'mis', vuelve a misIncidencias. Si no, a tecnicoIncidencias.
                    $link_volver = ($origen == 'mis') ? "misIncidencias.php" : "tecnicoIncidencias.php";
                ?>
                <a href="<?php echo $link_volver; ?>" class="<?php echo ($pagina_activa == 'incidencias') ? 'activo' : ''; ?>"><img src="IMGBLANCO/incidencia.png" alt="icono de un globo con una exclamación en medio">Incidencias/Solicitudes</a>
                
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
                    <h1>Gestionar Ticket #<?php echo $ticket['IdTicket']; ?></h1>
                </span>
    
            </header>

            <section class="main-content">
                
                <article class="form-container">

                    <a href="tecnicoIncidencias.php" class="back-link">&larr; Volver al panel</a>
                    <hr style="margin: 15px 0;">

                    <section class="ticket-details">
                        <h3>Descripción del Usuario</h3>
                        <p><strong>Usuario:</strong> <?php echo htmlspecialchars($ticket['Nombre'] . ' ' . $ticket['Apellidos']); ?></p>
                        <p><strong>Equipo:</strong> <?php echo htmlspecialchars($ticket['NumeroEquipo']); ?></p>
                        <p><strong>Fecha de inicio:</strong> <?php echo $ticket['FechaInicio']; ?></p>
                        <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($ticket['Descripcion'])); ?></p>
                    </section>

                    <form action="actualizarTicket.php" method="POST" class="form-gestion">
                        <input type="hidden" name="id_ticket" value="<?php echo $ticket['IdTicket']; ?>">

                        <section class="form-grid"> 
                            <p>
                                <label for="estado">Estado del Ticket:</label>
                                <select name="estado" id="estado">
                                    <option value="en curso" <?php if ($ticket['Estado'] == 'en curso') echo 'selected'; ?>>En curso</option>
                                    <option value="en espera" <?php if ($ticket['Estado'] == 'en espera') echo 'selected'; ?>>En espera</option>
                                    <option value="finalizado" <?php if ($ticket['Estado'] == 'finalizado') echo 'selected'; ?>>Finalizado</option>
                                </select>
                            </p>

                            <p>
                                <label for="tecnico_asignado">Técnico Asignado:</label>
                                <select name="tecnico_asignado" id="tecnico_asignado">
                                    <option value="">-- Sin Asignar --</option>
                                    <?php
                                    while ($tecnico = $resultado_tecnicos->fetch_assoc()) {
                                        $selected = ($tecnico['IdUsuario'] == $ticket['IdTecnicoAsignado']) ? 'selected' : '';
                                        echo "<option value='" . $tecnico['IdUsuario'] . "' $selected>";
                                        echo htmlspecialchars($tecnico['Nombre'] . ' ' . $tecnico['Apellidos']);
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                            </p>
                        </section> <p class="full-width">
                            <label for="comentario">Comentarios del Técnico:</label>
                            <textarea name="comentario" id="comentario" rows="6"><?php echo htmlspecialchars($ticket['Comentario'] ?? ''); ?></textarea>
                        </p>

                        <button type="submit">Guardar Cambios</button>
                    </form>

                </article> </section> </main> </section> </body>
</html>