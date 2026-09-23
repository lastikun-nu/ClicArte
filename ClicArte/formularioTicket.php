<?php
// formularioTicket.php
session_start();
$pagina_activa = 'incidencias'; // Para el menú
require 'conexion.php';

// 1. Seguridad: Proteger la página
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || ($rol_usuario_logueado != 'usuario' && $rol_usuario_logueado != 'profesor' && $rol_usuario_logueado != 'tecnico')) {
    header("Location: index.php?error=Acceso denegado");
    exit;
}
$id_usuario_logueado = $_SESSION['id_usuario'];

// 3. Consulta para el desplegable de Equipos
$sql_equipos = "SELECT IdEquipo, Tipo, NumeroEquipo 
                FROM Equipo 
                WHERE IdUsuarioAsignado = ?";
$stmt_equipos = $conn->prepare($sql_equipos);
$stmt_equipos->bind_param("i", $id_usuario_logueado);
$stmt_equipos->execute();
$resultado_equipos = $stmt_equipos->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crear Incidencia</title>
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
                    <h1>Nueva Incidencia</h1>
                </span>
                <span class="user-info">
                    <?php echo ucfirst($rol_usuario_logueado); ?>
                </span>
            </header>
            
            <section class="main-content">
                
                <article class="form-card">
                    <h2 style="margin-top: 0; color: #333; margin-bottom: 20px;">Detalles del problema</h2>
                    
                    <form action="guardarTicket.php" method="POST">
                    
                        <p class="form-group">
                            <label for="tipo">¿Qué necesitas?</label>
                            <select name="tipo" id="tipo">
                                <option value="Incidencia">Reportar una Incidencia (Algo no funciona)</option>
                                <option value="Solicitud">Hacer una Solicitud (Necesito algo nuevo)</option>
                            </select>
                        </p>

                        <p class="form-group">
                            <label for="id_equipo">Equipo afectado:</label>
                            <select name="id_equipo" id="id_equipo" required>
                                <option value="">-- Selecciona el equipo --</option>
                                <?php
                                if ($resultado_equipos->num_rows > 0) {
                                    while ($equipo = $resultado_equipos->fetch_assoc()) {
                                        echo "<option value='" . $equipo['IdEquipo'] . "'>";
                                        echo $equipo['Tipo'] . " (" . $equipo['NumeroEquipo'] . ")";
                                        echo "</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No tienes equipos asignados</option>";
                                }
                                ?>
                            </select>
                        </p>
                        
                        <p class="form-group">
                            <label for="descripcion">Descripción detallada:</label>
                            <textarea name="descripcion" id="descripcion" rows="6" placeholder="Describe el problema o solicitud con el mayor detalle posible..." required></textarea>
                        </p>

                        <button type="submit" class="btn-submit">Enviar Ticket</button>
                    </form>
                </article> </section> </main> </section> </body>
</html>