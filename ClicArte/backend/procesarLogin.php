<?php
// procesarLogin.php
session_start();
require 'conexion.php'; // Incluye la conexión 

// Solo debe funcionar si se envían datos por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// 1. Obtener datos del formulario
	$correo = $_POST['Correo'];
	$contrasena = $_POST['Contrasena'];

	// 2. Consultar la BD (La consulta "inteligente" que ya teníamos)
	$sql = "SELECT IdUsuario, Rol, Contrasena FROM Usuario WHERE Correo = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $correo); // "s" por string
	$stmt->execute();
	$resultado = $stmt->get_result();

	if ($resultado->num_rows === 1) {
		$usuario = $resultado->fetch_assoc();
		
		// 3. Verificar la contraseña (Seguimos con texto plano por ahora)
		if ($contrasena == $usuario['Contrasena']) {
			
			// 4. ¡ÉXITO! Guardar datos en la sesión
			$_SESSION['id_usuario'] = $usuario['IdUsuario'];
			$_SESSION['rol'] = $usuario['Rol']; // Aquí detecta 'Tecnico', 'Profesor', etc.
			
			// 5. Redirigir según el ROL
			$rol = strtolower($usuario['Rol']);
			
			// TODOS LOS ROLES van al dashboard, el dashboard sabrá qué mostrar
			header("Location: dashboard.php");
			exit;
			
		} else {
			// Error de contraseña: redirige de vuelta a index.php con un mensaje
			header("Location: index.php?error=Contraseña incorrecta");
			exit;
		}
	} else {
		// Error de correo: redirige de vuelta a index.php con un mensaje
		header("Location: index.php?error=Correo no encontrado");
		exit;
	}
	$stmt->close();
	$conn->close();

} else {
	// Si alguien intenta acceder a este archivo directamente
	header("Location: index.php");
	exit;
}
?>
