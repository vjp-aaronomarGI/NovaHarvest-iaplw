-- 1. Creación de la tabla excedentes
CREATE TABLE IF NOT EXISTS excedentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto VARCHAR(100) NOT NULL,
    productor VARCHAR(100) NOT NULL,
    pueblo VARCHAR(100) NOT NULL,
    kilos INT NOT NULL DEFAULT 0,
    precio_kg DECIMAL(6,2) NOT NULL
);

-- 2. Creación de la tabla avisos
CREATE TABLE IF NOT EXISTS avisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    producto VARCHAR(100) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Inserción de excedentes iniciales
INSERT INTO excedentes (producto, productor, pueblo, kilos, precio_kg) VALUES
('Cereza Picota', 'Huerta Los Castaños', 'Cabezuela del Valle', 223, 2.24),
('Castaña', 'Finca El Rebollar', 'Rebollar', 150, 2.10),
('Higo', 'Huerto La Garganta', 'Navaconcejo', 35, 2.80),
('Tomate de colgar', 'Hermanos Sánchez', 'Jerte', 80, 1.10),
('Calabacín', 'Huerta Los Castaños', 'Cabezuela del Valle', 0, 0.90);