<?php
// index.php
session_start();

// Si el usuario YA está logueado, lo mandamos directo al historial
if (isset($_SESSION['id_usuario'])) {
    header("Location: historial.php");
    exit;
}

// Comprobamos si venimos de un error de login
$error_login = null;
if (isset($_GET['error'])) {
    $error_login = htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClicArte - Iniciar Sesión</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body class="body-login">

    <section class="login-wrapper">
        
        <header class="logo-header">
            <img src="IMGBLANCO/logo_completo_negativp.png" alt="Logo de Campus">
        </header>

        <main class="login-card">
            
            <h2>Iniciar Sesión</h2>

            <form action="procesarLogin.php" method="POST">
                
                <p class="input-group">
                    <label for="correo">Correo Electrónico</label>
                    <input type="text" id="correo" name="Correo" placeholder="ejemplo@campus.com" required>
                </p>
                
                <p class="input-group">
                    <label for="pass">Contraseña</label>
                    <input type="password" id="pass" name="Contrasena" placeholder="••••••••" required>
                </p>
                
                <?php
                if ($error_login) {
                    echo "<p class='mensaje-error'>" . $error_login . "</p>";
                }
                ?>
                
                <button type="submit" class="btn-login">ACCEDER</button>
            </form>
        </main>

    </section> </body>
</html>
