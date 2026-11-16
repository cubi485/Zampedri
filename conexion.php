<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conexion = new mysqli("localhost", "root", "", "zampedri");

if ($conexion->connect_error) {
    die("No se pudo conectar a la base de datos.");
}

$conexion->set_charset("utf8mb4");

// evitar HTML
function limpiar($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
}
?>
