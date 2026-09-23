<?php
// guardarUsuario.php
session_start();
require 'conexion.php';

// 1. Seguridad: Solo Profesores y Técnicos pueden añadir usuarios
$rol_usuario_logueado = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
if (!isset($_SESSION['id_usuario']) || ($rol_usuario_logueado != 'profesor' && $rol_usuario_logueado != 'tecnico')) {
	header("Location: index.php?error=Acceso denegado");
	exit;
}

// 2. Verificar que los datos lleguen por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// 3. Recoger datos del formulario
	$nombre = $_POST['Nombre'];
	$apellidos = $_POST['Apellidos'];
	$correo = $_POST['Correo'];
	$contrasena = $_POST['Contrasena']; // (texto plano prueba)
	$rol = $_POST['Rol'];
	$curso = $_POST['Curso'];
	$turno = $_POST['Turno'];

	// 4. VALIDACIÓN: Comprobar si el correo ya existe (es UNIQUE)
	$sql_check = "SELECT IdUsuario FROM Usuario WHERE Correo = ?";
	$stmt_check = $conn->prepare($sql_check);
	$stmt_check->bind_param("s", $correo);
	$stmt_check->execute();
	$resultado_check = $stmt_check->get_result();
	
	if ($resultado_check->num_rows > 0) {
		// El correo ya existe
		$stmt_check->close();
		$conn->close();
		header("Location: gestionAlumnos.php?error=Error: El correo " . urlencode($correo) . " ya está registrado.");
		exit;
	}
	$stmt_check->close();
	
	// 5. Si el correo no existe, proceder con la INSERCIÓN
	$sql_insert = "INSERT INTO Usuario (Nombre, Apellidos, Correo, Contrasena, Rol, Curso, Turno) 
				   VALUES (?, ?, ?, ?, ?, ?, ?)";
	
	$stmt_insert = $conn->prepare($sql_insert);
	// s = string
	$stmt_insert->bind_param("sssssss", $nombre, $apellidos, $correo, $contrasena, $rol, $curso, $turno);
	
	if ($stmt_insert->execute()) {
		// Éxito
		$stmt_insert->close();
		$conn->close();
		header("Location: gestionAlumnos.php?success=1");
		exit;
	} else {
		// Error al insertar
		$stmt_insert->close();
		$conn->close();
		header("Location: gestionAlumnos.php?error=Error desconocido al guardar el usuario.");
		exit;
	}

} else {
	// Si no es POST, redirigir
	header("Location: gestionAlumnos.php");
	exit;
}
?>