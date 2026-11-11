<?php
// Configuración de la conexión mediante variables de entorno
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

// Conexión con PDO e inserción con sentencia preparada y parámetros con nombre
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("INSERT INTO avisos (nombre, email, producto) VALUES (:nombre, :email, :producto)");
$stmt->execute([
    ':nombre'   => $_POST['nombre'],
    ':email'    => $_POST['email'],
    ':producto' => $_POST['producto']
]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NovaHarvest - Aviso registrado</title>
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
        <h1>Aviso registrado</h1>
        <p>Gracias. Te avisaremos cuando haya excedentes del producto que buscas.</p>
        <p><a href="excedentes.php">Ver excedentes disponibles</a></p>
    </main>

    <footer>
        NovaHarvest · IES Valle del Jerte · Curso 2026-2027
    </footer>
</body>
</html>
