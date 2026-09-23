<?php
// actualizarTicket.php
session_start();
require 'conexion.php';

// 1. Seguridad: Solo Técnicos pueden actualizar
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || $rol_usuario_logueado != 'tecnico') {
	die("Acceso denegado.");
}
$id_tecnico_logueado = $_SESSION['id_usuario'];

// 2. Verificar que los datos lleguen por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// 3. Recoger datos del formulario
	$ticket_id = $_POST['id_ticket'];
	$estado_nuevo = $_POST['estado'];
	$comentario_nuevo = $_POST['comentario']; 
	$id_tecnico_asignado = $_POST['tecnico_asignado'];

	if (empty($id_tecnico_asignado)) {
		$id_tecnico_asignado = NULL;
	}

	// 4. Determinar si el ticket se está cerrando AHORA
	$fecha_fin = NULL;
	if ($estado_nuevo == 'finalizado') {
		$fecha_fin = date('Y-m-d H:i:s');
	}

	// 5. Preparar la consulta SQL
	$sql = "UPDATE Ticket 
			SET 
				Estado = ?, 
				Comentario = ?, 
				IdTecnicoAsignado = ?, 
				FechaFin = ?
			WHERE 
				IdTicket = ?";
	
	$stmt = $conn->prepare($sql);
	// Tipos: s (string), s (string), i (integer), s (string), i (integer)
	$stmt->bind_param("ssisi", $estado_nuevo, $comentario_nuevo, $id_tecnico_asignado, $fecha_fin, $ticket_id);
	
	// 6. Ejecutar y redirigir
	if ($stmt->execute()) {
		header("Location: tecnicoIncidencias.php?success=Ticket actualizado");
	} else {
		echo "Error al actualizar el ticket: " . $stmt->error;
	}
	
	$stmt->close();
	$conn->close();

} else {
	header("Location: tecnicoIncidencias.php");
	exit;
}
?>
