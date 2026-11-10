<?php
// Configuración de la conexión mediante variables de entorno
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Recoger variables de $_POST
    $nombre   = $_POST['nombre'] ?? '';
    $email    = $_POST['email'] ?? '';
    $producto = $_POST['producto'] ?? '';

    // Validar que no lleguen campos vacíos si se saltan el navegador
    if (!empty($nombre) && !empty($email) && !empty($producto)) {
        // Prepared statement con parámetros con nombre
        $stmt = $pdo->prepare("INSERT INTO avisos (nombre, email, producto) VALUES (:nombre, :email, :producto)");
        $stmt->execute([
            ':nombre'   => $nombre,
            ':email'    => $email,
            ':producto' => $producto
        ]);
    }
} catch (PDOException $e) {
    // En producción display_errors = Off ocultará el detalle del error en pantalla
    echo "Error al registrar el aviso en la base de datos.";
    exit;
}
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
        <img src="img/logo.png" alt="Logo NovaHarvest">
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
        <p><a href="excedentes.php">Volver al listado de excedentes</a></p>
    </main>

    <footer>
        NovaHarvest · IES Valle del Jerte · Curso 2026-2027
    </footer>
</body>
</html>
