<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información del Servidor</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <?php
    date_default_timezone_set('Europe/Madrid');
    
    $fecha_hora = date('d/m/Y H:i:s');
    
    $contenedor_php = getenv('HOSTNAME') ?: 'No detectado';
    ?>

    <div class="info-box">
        <p><strong>Fecha y hora del servidor:</strong></p>
        <p><?php echo $fecha_hora; ?></p>
        
        <br>
        
        <p><strong>Contenedor PHP:</strong> <?php echo htmlspecialchars($contenedor_php); ?></p>
    </div>

</body>
</html>
