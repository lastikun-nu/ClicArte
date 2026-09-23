<?php
// guardarEquipo.php
session_start();
require 'conexion.php';

// 1. Seguridad: Solo Técnicos pueden añadir equipos
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || $rol_usuario_logueado != 'tecnico') {
	header("Location: index.php?error=Acceso denegado");
	exit;
}

// 2. Verificar que los datos lleguen por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// 3. Recoger datos del formulario
	$planta = $_POST['Planta'];
	$aula = $_POST['Aula'];
	$ubicacion = $_POST['Ubicacion'];
	$tipo = $_POST['Tipo'];
	$numeroSerie = $_POST['NumeroSerie'];
	$numeroEquipo = $_POST['NumeroEquipo'];
	$estado = $_POST['Estado'];
	$idUsuarioAsignado = $_POST['IdUsuarioAsignado'];
	
	// 4. Limpieza de datos
	// Si el usuario asignado está vacío, lo ponemos como NULL
	if (empty($idUsuarioAsignado)) {
		$idUsuarioAsignado = NULL;
	}
	// Si la planta está vacía, la ponemos como NULL
	if (empty($planta)) {
		$planta = NULL;
	}

	// 5. VALIDACIÓN: Comprobar si el Nº de Equipo ya existe (debería ser único)
	$sql_check = "SELECT IdEquipo FROM Equipo WHERE NumeroEquipo = ?";
	$stmt_check = $conn->prepare($sql_check);
	$stmt_check->bind_param("s", $numeroEquipo);
	$stmt_check->execute();
	$resultado_check = $stmt_check->get_result();
	
	if ($resultado_check->num_rows > 0) {
		$stmt_check->close();
		$conn->close();
		header("Location: tecnicoEquipos.php?error=Error: El Nº de Equipo '" . urlencode($numeroEquipo) . "' ya existe.");
		exit;
	}
	$stmt_check->close();
	
	// 6. Si no existe, proceder con la INSERCIÓN
	$sql_insert = "INSERT INTO Equipo (Planta, Aula, Ubicacion, Tipo, NumeroSerie, NumeroEquipo, Estado, IdUsuarioAsignado) 
				   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
	
	$stmt_insert = $conn->prepare($sql_insert);
	// Tipos: i (int), s (string), s, s, s, s, s, i (int)
	$stmt_insert->bind_param("issssssi", $planta, $aula, $ubicacion, $tipo, $numeroSerie, $numeroEquipo, $estado, $idUsuarioAsignado);
	
	if ($stmt_insert->execute()) {
		// Éxito
		$stmt_insert->close();
		$conn->close();
		header("Location: tecnicoEquipos.php?success=1");
		exit;
	} else {
		// Error al insertar
		$stmt_insert->close();
		$conn->close();
		header("Location: tecnicoEquipos.php?error=Error desconocido al guardar el equipo.");
		exit;
	}

} else {
	// Si no es POST, redirigir
	header("Location: tecnicoEquipos.php");
	exit;
}
?>