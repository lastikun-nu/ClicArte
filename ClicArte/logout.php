<?php
// logout.php
session_start();
session_destroy();
header("Location: index.php"); // Manda al usuario de vuelta al login
exit;
?>