# NovaHarvest

Plataforma web para la gestión y publicación de excedentes agrícolas de la cooperativa del Valle del Jerte. Permite a los productores mostrar sus cosechas disponibles y a los compradores solicitar avisos cuando haya stock de los productos que necesitan.

---

## 1. Arranque

Para desplegar el entorno completo desde un clon recién descargado del repositorio, solo necesitas tener instalados **Docker**, **Docker Compose** y **OpenSSL**.

### Pasos de puesta en marcha

1. **Clonar el repositorio y entrar en la carpeta del proyecto:**
   ```bash
   git clone https://github.com
   cd NovaHarvest-iaplw
   ```

2. **Crear el fichero de variables de entorno:**
   Copia la plantilla `.env.example` a `.env` (si lo deseas, puedes editar las contraseñas en este paso):
   ```bash
   cp .env.example .env
   ```

3. **Generar los certificados TLS autofirmados para HTTPS:**
   Crea la carpeta `nginx/certs` y genera el certificado y la clave privada para `localhost`:
   ```bash
   mkdir -p nginx/certs
   openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
     -keyout nginx/certs/novaharvest.key \
     -out nginx/certs/novaharvest.crt \
     -subj "/CN=localhost"
   ```

4. **Construir las imágenes y levantar los contenedores:**
   ```bash
   docker compose up -d --build
   ```

5. **Comprobar que los servicios están activos y `db` está saludable (`healthy`):**
   ```bash
   docker compose ps
   ```

6. **Cargar la estructura inicial, datos y permisos en MariaDB:**
   Sustituye `tu_clave_root` por el valor de `DB_ROOT_PASSWORD` definido en tu `.env` (sin espacio después de `-p`):
   ```bash
   docker compose exec -T db mariadb -u root -prootnovaharvest < db/novaharvest.sql
   ```

### URLs de acceso y comprobación

* **Portada (Inicio):** `https://localhost:8443/` *(el acceso por `http://localhost:8080/` redirige automáticamente con código `301`)*
* **Listado de excedentes:** `https://localhost:8443/excedentes.php`
* **Formulario de avisos:** `https://localhost:8443/avisos.html`
* **Calendario de temporada:** `https://localhost:8443/temporada.md`
* **Estado del servidor:** `https://localhost:8443/estado.php`
* **Adminer (gestión de BD, solo local):** `http://localhost:8081` *(Sistema: MariaDB, Servidor: `db`, Usuario: `novaharvest_app`, Base de datos: `novaharvest`)*

### Parada y reinicio del entorno

* **Detener los contenedores sin perder los datos de la base de datos:**
  ```bash
  docker compose down
  ```
* **Detener los contenedores y borrar el volumen de datos para empezar de cero:**
  ```bash
  docker compose down -v
  ```

---

## 2. Arquitectura

La infraestructura se despliega mediante cuatro contenedores aislados en la red interna de Docker Compose. Solo el servidor web `web` y la herramienta `adminer` (restringida a `127.0.0.1`) exponen puertos hacia el exterior.

```text
+-------------------------------------------------------------------+

|                        MÁQUINA ANFITRIONA                         |
|                                                                   |
|   [ Navegador Web ]                                               |
|      |         |                                                  |
|      | HTTP    | HTTPS (8443)           [ Navegador Admin ]       |
|      | (8080)  |                              |                   |
|      v         v                              v (127.0.0.1:8081)  |
+------|---------|------------------------------|-------------------+

       |         |                              |
=======|=========|==============================|====================

       |         |     RED INTERNA DOCKER       |
       v         v                              v
+-----------------------+               +-----------------------+

|      Servicio: web    |               |   Servicio: adminer   |
|  (nginx:1.27-alpine)  |               |      (adminer:5)      |
|  Puertos: 80, 443     |               |  Puerto interno: 8080 |
+-----------------------+               +-----------------------+

    |               |                               |
    | Estáticos     | FastCGI (TCP 9000)            | SQL (TCP 3306)
    v               v                               |
[ ./web:ro ]   +-----------------------+            |

               |     Servicio: php     |            |
               | (php:8.3-fpm-alpine)  |            |
               |  Puerto interno: 9000 |            |
               +-----------------------+            |

                   |               |                |
                   | Lectura       | SQL (TCP 3306) |
                   v               v                v
               [ ./web ]      +---------------------------+

                              |       Servicio: db        |
                              |       (mariadb:11)        |
                              |    Puerto interno: 3306   |
                              +---------------------------+
                                           |
                                           v
                                  [ Volumen: dbdata ]
                                   (/var/lib/mysql)
```

### Recorrido de una petición a `excedentes.php`

1. El cliente solicita `https://localhost:8443/excedentes.php` y el tráfico llega al puerto `443` del contenedor **`web`** (nginx), que descifra la conexión TLS usando los certificados montados en `/etc/nginx/certs/`.
2. Al coincidir la URI con la regla `location ~ \.php$`, nginx no sirve el fichero como texto, sino que reenvía la petición mediante el protocolo **FastCGI** al contenedor **`php`** por el puerto interno `9000`, pasando la ruta completa del script (`$document_root$fastcgi_script_name`).
3. El contenedor **`php`** (PHP-FPM) lee `/usr/share/nginx/html/excedentes.php` desde el volumen compartido `./web` y lo ejecuta.
4. El código PHP obtiene las credenciales desde sus variables de entorno (`DB_*`) y abre una conexión **PDO** por el puerto interno `3306` hacia el host `db` (contenedor **`db`**).
5. **MariaDB** consulta los datos persistidos en el volumen `dbdata` (`SELECT ... WHERE kilos > 0`) y devuelve las filas a PHP-FPM.
6. PHP-FPM genera el documento HTML resultante y lo devuelve a nginx por FastCGI, que finalmente lo envía cifrado por HTTPS al navegador del usuario.

---

## 3. Configuración

A continuación se detallan todos los ficheros de configuración versionados en el repositorio y su función dentro de los contenedores:

| Fichero en el repositorio | Servicio que lo usa | Ruta dentro del contenedor | Qué configura |
|---|---|---|---|
| `compose.yaml` | Docker Compose | *(Orquestador en anfitrión)* | Define los servicios (`web`, `php`, `db`, `adminer`), imágenes, puertos, volúmenes, variables de entorno y dependencias de salud. |
| `.env.example` | Docker Compose | *(Plantilla en anfitrión)* | Define las variables de entorno necesarias (`DB_NAME`, `DB_USER`, `DB_PASS`, `DB_ROOT_PASSWORD`) sin contraseñas reales. |
| `nginx/default.conf` | `web` | `/etc/nginx/conf.d/default.conf` | Redirección 301 de HTTP a HTTPS (8443), TLS, `root`, páginas de error (`404`/`50x`), `gzip`, tipo MIME para `.md`, límite de 64 KB y paso a PHP-FPM. |
| `php/Dockerfile` | `php` | *(Construcción de imagen)* | Parte de `php:8.3-fpm-alpine` e instala las extensiones `pdo_mysql` y `mysqli` para conectar con MariaDB. |
| `php/www.conf` | `php` | `/usr/local/etc/php-fpm.d/www.conf` | Configura el pool `[www]` con usuario `www-data`, escucha en el puerto `9000` y gestión de procesos `pm = dynamic` (5 hijos máximo). |
| `php/php.ini` | `php` | `/usr/local/etc/php/conf.d/novaharvest.ini` | Fija `memory_limit = 128M`, `error_reporting = E_ALL`, `display_errors = Off`, `log_errors = On` y `date.timezone = Europe/Madrid`. |
| `db/novaharvest.sql` | `db` | *(Ejecutado vía entrada estándar)* | Crea las tablas `excedentes` y `avisos`, inserta los 5 productos iniciales y restringe los permisos del usuario `novaharvest_app`. |

---

## 4. Base de datos

La base de datos se llama **`novaharvest`** y contiene dos tablas principales:

* **`excedentes`**: Almacena los productos disponibles de la cooperativa (`id`, `producto`, `productor`, `pueblo`, `kilos`, `precio_kg`).
* **`avisos`**: Registra las solicitudes enviadas por los compradores desde el formulario web (`id`, `nombre`, `email`, `producto`, `fecha`).

### Usuario de la aplicación y privilegios

La aplicación web nunca se conecta como `root`, sino mediante el usuario **`novaharvest_app`** con privilegios mínimos:

* **`SELECT` sobre `novaharvest.*`**: Necesario para leer el catálogo de productos en `excedentes.php`.
* **`INSERT` sobre `novaharvest.*`**: Necesario para registrar nuevas solicitudes en la tabla `avisos` desde `avisos.php`.

Se deniegan permisos destructivos o administrativos (`DELETE`, `UPDATE`, `DROP`, `ALTER`) para que, ante un posible fallo o vulnerabilidad en la aplicación web, un atacante no pueda borrar ni modificar los registros existentes ni la estructura de las tablas.

### Cómo comprobar los privilegios

Para verificar los permisos activos del usuario de la aplicación, ejecuta:

```bash
docker compose exec db mariadb -u root -p -e "SHOW GRANTS FOR 'novaharvest_app'@'%';"
```

La salida debe mostrar únicamente `USAGE` global y `GRANT SELECT, INSERT ON novaharvest.* TO 'novaharvest_app'@'%'`.
