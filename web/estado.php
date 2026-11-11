<?php
// Configuración de la conexión mediante variables de entorno
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

// Conexión con PDO como novaharvest_app y consulta de la versión de MariaDB
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query("SELECT VERSION()");
$version_db = $stmt->fetchColumn();

// Obtención de fecha/hora (zona horaria fijada en php.ini) y nombre del contenedor
$fecha_hora = date('d/m/Y H:i:s');
$contenedor = getenv('HOSTNAME');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NovaHarvest - Estado del servidor</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <img src="img/logo.png" alt="Logotipo de NovaHarvest">
        <span>NovaHarvest</span>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="excedentes.php">Excedentes</a>
            <a href="avisos.html">Avísame</a>
            <a href="temporada.md">Temporada</a>
            <a href="estado.php">Estado</a>
        </nav>
    </header>

    <main>
        <h1>Estado del servidor</h1>
        <p>Fecha y hora del servidor: <?= $fecha_hora ?></p>
        <p>Contenedor PHP: <?= $contenedor ?></p>
        <p>Base de datos: MariaDB <?= $version_db ?></p>
        <p>Página generada por PHP-FPM</p>
    </main>

    <footer>
        NovaHarvest · IES Valle del Jerte · Curso 2026-2027
    </footer>
</body>
</html>
