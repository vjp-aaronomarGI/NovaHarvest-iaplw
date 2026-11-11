<?php
// Configuración de la conexión mediante variables de entorno
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

// Conexión con PDO y consulta de excedentes con más de 0 kilos
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query("SELECT producto, productor, pueblo, kilos, precio_kg FROM excedentes WHERE kilos > 0");
$excedentes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NovaHarvest - Excedentes disponibles</title>
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
        <h1>Excedentes disponibles</h1>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Productor</th>
                    <th>Pueblo</th>
                    <th>Kilos</th>
                    <th>Precio/kg</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($excedentes as $fila): ?>
                    <tr>
                        <td><?= $fila['producto'] ?></td>
                        <td><?= $fila['productor'] ?></td>
                        <td><?= $fila['pueblo'] ?></td>
                        <td><?= $fila['kilos'] ?></td>
                        <td><?= number_format($fila['precio_kg'], 2, ',', '.') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer>
        NovaHarvest · IES Valle del Jerte · Curso 2026-2027
    </footer>
</body>
</html>
