<?php
// guardarTicket.php
session_start();

// 1. Verificar que el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
	die("Error: No tienes permiso para hacer esto. Inicia sesión.");
}

// 2. Incluir la conexión
require 'conexion.php';

// 3. Recoger TODOS los datos
// 	  Datos de la SESIÓN:
$id_usuario = $_SESSION['id_usuario']; 

// 	  Datos del FORMULARIO:
$tipo = $_POST['tipo'];
$id_equipo = $_POST['id_equipo'];
$descripcion = $_POST['descripcion'];

// 4. Preparar la consulta SQL
$sql = "INSERT INTO Ticket 
			(IdUsuario, IdEquipo, Tipo, Descripcion, FechaInicio, Estado) 
		VALUES 
			(?, ?, ?, ?, NOW(), ?)"; // NOW() pone la fecha y hora actual

$stmt = $conn->prepare($sql);

// 5. Vincular los parámetros
$estado_inicial = "en curso";
$stmt->bind_param("iisss", $id_usuario, $id_equipo, $tipo, $descripcion, $estado_inicial);

// 6. Ejecutar y verificar
if ($stmt->execute()) {
	// Redirigir al historial
	header("Location: misIncidencias.php"); 
} else {
	echo "Error al crear el ticket: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
