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
    
    // Seleccionar solo los excedentes con más de 0 kilos
    $query = $pdo->query("SELECT producto, productor, pueblo, kilos, precio_kg FROM excedentes WHERE kilos > 0");
    $excedentes = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción display_errors = Off ocultará el detalle del error en pantalla
    echo "Error en la conexión o consulta con la base de datos.";
    exit;
}
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
        <img src="img/logo.png" alt="Logo NovaHarvest">
        <span>NovaHarvest</span>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="excedentes.php">Excedentes</a>
            <a href="avisos.php">Avísame</a>
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
                <?php foreach ($excedentes as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['producto']) ?></td>
                        <td><?= htmlspecialchars($item['productor']) ?></td>
                        <td><?= htmlspecialchars($item['pueblo']) ?></td>
                        <td><?= htmlspecialchars($item['kilos']) ?></td>
                        <td><?= number_format($item['precio_kg'], 2, ',', '.') ?> €</td>
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