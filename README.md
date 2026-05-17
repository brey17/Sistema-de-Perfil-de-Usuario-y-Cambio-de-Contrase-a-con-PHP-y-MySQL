Sistema de Login con PHP y MySQL

Descripcion

El sistema tiene las siguientes funciones:

- Registro de nuevos usuarios (no deja registrar dos veces el mismo correo).
- Login con correo y contrasena.
- Las contrasenas se guardan encriptadas con "password_hash" (no se guardan en texto plano).
- Hay una zona privada que solo se ve si estas logueado, si no estas logueado te redirige al login.
- Se puede actualizar el nombre y el correo desde el perfil.
- Hay una opcion para cambiar la contrasena (pide la actual y la nueva).
- Boton para cerrar sesion que destruye la sesion.

Para los estilos use Bootstrap 5 desde CDN porque me parecio mas facil que hacer todo el css a mano.
Requisitos

Para correrlo necesitas:

- PHP (yo use la version 8.2 pero deberia servir con 7.4 en adelante).
- MySQL (o MariaDB).
- Un servidor web como Apache o el que trae PHP integrado.
- Un navegador.
- Conexion a internet para que cargue Bootstrap.

Tambien lo deje preparado para correrlo con **Docker** que es mas facil porque no toca instalar nada manual.

Pasos para instalarlo

Forma facil: con Docker

Si tienes Docker Desktop instalado solo haces:

1. Clonas el repositorio:

   ```bash
   git clone <url-del-repositorio>
   cd sistema-login
   ```

2. Abres Docker Desktop y esperas a que prenda.

3. Corres este comando:

   ```bash
   docker compose up -d
   ```

   La primera vez se demora porque descarga las imagenes. Esto levanta PHP con Apache, MySQL y phpMyAdmin. La base de datos se crea sola con el archivo "sql/database.sql".

4. Abres en el navegador:
   - El sistema: http://localhost:8080
   - phpMyAdmin (para ver la base de datos): http://localhost:8081
     - Usuario: "root"
     - Contrasena: "root"

5. Cuando quieras apagarlo:

   ```bash
   docker compose down
   ```

Forma tradicional: con XAMPP

1. Instalas XAMPP (o MAMP si estas en Mac).

2. Copias la carpeta del proyecto dentro de "htdocs" de XAMPP.

3. Prendes Apache y MySQL desde el panel de XAMPP.

4. Entras a http://localhost/phpmyadmin e importas el archivo "sql/database.sql" para crear la base de datos.

5. Si tu MySQL tiene otra contrasena, abres el archivo "config/conexion.php" y le cambias los datos:

   ```php
   $host = "localhost";
   $usuario_db = "root";
   $password_db = "";
   $nombre_db = "sistema_login";
   ```

6. Entras a http://localhost/sistema-login en el navegador.

Como probarlo

1. Le das clic en "Registrate aqui" desde la pagina de login.
2. Llenas el formulario con cedula, nombre, correo y contrasena.
3. Te logueas con el correo y la contrasena.
4. En el perfil puedes cambiar el nombre o el correo y darle guardar.
5. Tambien puedes ir a "Cambiar Contrasena" para probar esa funcion (te pide la contrasena vieja).
6. Le das clic en "Cerrar Sesion" del menu de arriba para salir.
