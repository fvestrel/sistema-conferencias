# Sistema de gestión de conferencias

Solución académica en PHP orientado a objetos y MySQL/MariaDB, para ejecutar con XAMPP y editar con Visual Studio Code. Incluye crear, consultar, editar y eliminar registros en cuatro secciones. No requiere Composer ni paquetes externos.

La presentación utiliza un diseño sencillo: título, enlaces, formularios en una columna y tablas con bordes.

## Actualizar a la versión MVC

Conserva tu configuración de `config/database.php` si cambiaste la contraseña o el puerto. Sustituye `public` y `src` por las carpetas de esta versión y conserva `config` con tus datos de conexión. La reorganización MVC no cambia las tablas ni requiere volver a importar la base de datos. La dirección sigue siendo `http://localhost/sistema-conferencias/public/`.

La vista previa HTML contiene datos de ejemplo y no guarda cambios; la aplicación PHP completa se ejecuta con XAMPP.

## Si todavía tienes la tabla Ponentes

Si tu base todavía tiene la tabla `Ponentes`, exporta una copia de seguridad desde phpMyAdmin y después importa **una sola vez** `database/actualizar_a_conferencistas.sql`. Este archivo cambia los nombres de la tabla y los identificadores conservando los registros y sus relaciones. Sustituye también los archivos del proyecto por esta versión.

Si es tu primera instalación, importa únicamente `database/esquema.sql`, como se explica abajo. No importes ambos archivos ni uses el esquema nuevo para actualizar la estructura anterior.

## 1. Diseño de la base de datos

El enunciado proporciona Conferencias y Asistentes. Se amplía con Conferencistas e Inscripciones, según la ampliación acordada. Son entidades adicionales; no surgen automáticamente de las tablas iniciales.

| Tabla | Clave primaria | Otros campos |
|---|---|---|
| Conferencistas | conferencista_id | nombre, apellido, especialidad, email |
| Conferencias | conferencia_id | nombre, fecha, ubicacion, descripcion, conferencista_id |
| Asistentes | asistente_id | nombre, apellido, email, telefono |
| Inscripciones | inscripcion_id | conferencia_id, asistente_id, fecha_inscripcion |

Relaciones:

```text
Conferencistas (1) ───── (N) Conferencias
Conferencias (1) ───── (N) Inscripciones (N) ───── (1) Asistentes
```

Un conferencista puede impartir varias conferencias; para este ejercicio, cada conferencia tiene un conferencista. Un asistente puede inscribirse en varias conferencias y una conferencia puede recibir varios asistentes. Esta relación de muchos a muchos se resuelve con Inscripciones. Si se requirieran varios conferencistas por conferencia, haría falta otra tabla intermedia.

La combinación conferencia_id + asistente_id es única: evita inscripciones duplicadas. Las claves foráneas impiden referencias a registros inexistentes. ON DELETE RESTRICT impide borrar registros relacionados. El teléfono se almacena como texto para conservar ceros iniciales y prefijos.

## 2. Preparar XAMPP

1. Abre XAMPP e inicia Apache y MySQL. En macOS, usa la pestaña Manage Servers del administrador de XAMPP.
2. Copia la carpeta completa `sistema-conferencias` dentro de la carpeta `htdocs` de tu instalación:
   - Windows, ruta habitual: `C:\xampp\htdocs\sistema-conferencias`
   - macOS, ruta habitual: `/Applications/XAMPP/xamppfiles/htdocs/sistema-conferencias`
3. Abre Visual Studio Code y selecciona Archivo → Abrir carpeta → `sistema-conferencias`.

No hace falta Visual Studio para esta solución en PHP. No abras index.php haciendo doble clic: debe ejecutarse mediante Apache.

## 3. Crear la base de datos

1. Abre `http://localhost/phpmyadmin/` en el navegador.
2. Selecciona la pestaña **Importar**.
3. Elige el archivo `database/esquema.sql` del proyecto.
4. Pulsa **Importar** o **Continuar**, según la versión.
5. Comprueba que aparece la base `gestion_conferencias` con sus cuatro tablas.

El archivo crea la base y las tablas sin borrar datos. Si ya tienes otra base con ese nombre y una estructura distinta, cambia el nombre tanto en el SQL como en la configuración antes de importar. CREATE TABLE IF NOT EXISTS no actualiza tablas antiguas.

XAMPP incluye MariaDB en lugar de MySQL; el esquema utilizado funciona con ambos. Véase la [documentación oficial de XAMPP](https://www.apachefriends.org/faq_windows.html).

## 4. Configurar la conexión

Abre `config/database.php`. La configuración inicial es:

Si descargaste el proyecto desde GitHub y el archivo no existe, copia `config/database.example.php` y renombra la copia a `database.php`. Mantén el archivo de ejemplo en el proyecto. El archivo con tu configuración local se excluye de Git.

```php
return [
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'gestion_conferencias',
    'username' => 'root',
    'password' => '',
];
```

Si asignaste una contraseña a MySQL o usas otro puerto, modifica esos valores. Estos son parámetros de conexión, no datos que debas escribir en phpMyAdmin. Se necesita PHP 8.0 o superior con pdo_mysql y mbstring, disponibles en la instalación de XAMPP comprobada.

## 5. Ejecutar y probar

Abre `http://localhost/sistema-conferencias/public/`. Si Apache utiliza otro puerto, inclúyelo, por ejemplo `http://localhost:8080/sistema-conferencias/public/`.

Sigue este orden para crear datos:

1. **Conferencistas:** registra a María Pérez, especialidad Programación, correo maria@example.com.
2. **Conferencias:** selecciona a María y registra Introducción a PHP, fecha 2026-12-15, ubicación Auditorio, descripción Clases y objetos.
3. **Asistentes:** registra a Ana López, correo ana@example.com, teléfono 0991234567.
4. **Inscripciones:** selecciona Introducción a PHP y Ana López; pulsa Registrar.
5. Verifica que cada registro aparece en su tabla. En las relaciones se muestran nombres en lugar de identificadores.
6. Pulsa Editar, cambia un dato y pulsa Guardar cambios. Comprueba el nuevo valor. Puedes editar también una inscripción para cambiar su conferencia o asistente.
7. Intenta registrar a Ana de nuevo en la misma conferencia: debe aparecer un aviso de duplicación.
8. Intenta eliminar esa conferencia mientras tenga la inscripción: debe aparecer un aviso de relación existente.
9. Para borrar los datos del ejemplo, elimina primero la inscripción, después la conferencia y finalmente el asistente y el conferencista.

La fecha de inscripción se asigna automáticamente al crearla. No cambia al editarla. La fecha de conferencia se almacena como DATE, sin hora.

## 6. Estructura y explicación de la programación orientada a objetos

```text
sistema-conferencias/
├── config/database.php
├── database/
├── public/
│   ├── index.php                  Punto de entrada
│   └── style.css                  Estilos
├── src/
│   ├── bootstrap.php              Carga las clases y funciones comunes
│   ├── Core/
│   │   ├── Database.php           Conexión con PDO
│   │   └── Vista.php              Renderiza las vistas
│   ├── Models/
│   │   ├── Registro.php           Clase abstracta del CRUD
│   │   ├── Conferencista.php
│   │   ├── Conferencia.php
│   │   ├── Asistente.php
│   │   └── Inscripcion.php
│   ├── Controllers/
│   │   └── RegistroController.php Controlador de las cuatro secciones
│   └── Views/
│       ├── layout/                Encabezado y cierre de página
│       └── registros/             Formulario y listado compartidos
└── tests/                         Pruebas de modelos y del flujo MVC
```

**Clases y objetos:** cada modelo representa un tipo de registro. Por ejemplo, `new Asistente($db)` crea un objeto que opera sobre Asistentes.

**Encapsulamiento:** la conexión y los detalles de las tablas son propiedades protegidas. Se utilizan métodos públicos para operar con los datos.

**Herencia y abstracción:** Conferencista, Conferencia, Asistente e Inscripcion heredan de Registro. Esta clase abstracta reúne las operaciones comunes y no se instancia directamente.

**Polimorfismo:** la página utiliza los mismos métodos `listar`, `buscar`, `guardar` y `eliminar` sobre cualquiera de los cuatro modelos. Cada uno aplica esas operaciones a su propia tabla.

## 7. Cómo funciona cada operación CRUD

| Operación | Método | SQL utilizado |
|---|---|---|
| Crear | guardar(datos) | INSERT INTO |
| Consultar | listar() / buscar(id) | SELECT |
| Actualizar | guardar(datos, id) | UPDATE |
| Eliminar | eliminar(id) | DELETE |

El formulario envía datos con POST a `public/index.php`. Este archivo entrega la solicitud a `RegistroController`, que valida la acción y el token del formulario, selecciona el modelo y llama al método correspondiente. El modelo valida los campos y ejecuta el SQL con PDO. Si la operación tiene éxito, el controlador indica una redirección al listado con HTTP 303 para evitar repetir el envío al actualizar el navegador.

**Modelo:** los archivos en `src/Models` gestionan los datos y las reglas del CRUD.

**Vista:** los archivos en `src/Views` muestran HTML a partir de los datos que reciben. No ejecutan consultas ni llaman a los modelos. El formulario y el listado se reutilizan para las cuatro secciones.

**Controlador:** `src/Controllers/RegistroController.php` coordina las solicitudes GET y POST, los modelos y los datos que se mostrarán. Se utiliza un solo controlador reutilizable porque las cuatro secciones comparten las mismas operaciones.

Ejemplo al editar una conferencia:

```text
Navegador → public/index.php → RegistroController → Conferencia → Base de datos
                                      ↓
                              Datos para la vista
                                      ↓
                           Views/registros/index.php → Navegador
```

Para cambiar la apariencia, edita las vistas y `public/style.css`. Para cambiar el tratamiento de una solicitud, edita el controlador. Para cambiar la validación de datos o las consultas, edita los modelos.

Las consultas utilizan parámetros preparados para separar los valores del SQL, según el [manual oficial de PDO](https://www.php.net/manual/es/pdo.prepare.php). La salida se escapa con htmlspecialchars. Los cambios utilizan POST y un token de sesión para comprobar el origen del formulario.

Esta versión usa envío tradicional de formularios y recarga después de guardar; AJAX no se exige en el enunciado visible. Es un ejercicio local sin inicio de sesión.

## 8. Problemas frecuentes

- **No se pudo abrir la base de datos:** inicia MySQL, importa el esquema y verifica usuario, contraseña y puerto.
- **Página no encontrada:** comprueba que la carpeta está dentro de htdocs y la URL termina en `/public/`.
- **No hay opciones de conferencistas:** registra primero un conferencista.
- **No hay conferencias o asistentes para inscribir:** crea primero esos registros.
- **No permite eliminar:** elimina o modifica primero las relaciones indicadas por el aviso.
- **Se ve código PHP como texto:** ejecuta mediante Apache, usando la URL local, y verifica su configuración de PHP.

## 9. Verificación del código

La prueba aislada de modelos se ejecuta desde la carpeta del proyecto:

```text
php tests/crud.php
php tests/mvc.php
```

En macOS, si PHP no está en la terminal:

```text
/Applications/XAMPP/xamppfiles/bin/php tests/crud.php
/Applications/XAMPP/xamppfiles/bin/php tests/mvc.php
```

Las pruebas usan SQLite en memoria para comprobar el CRUD, las restricciones y el flujo entre controlador y vistas sin modificar una base del usuario. No sustituye la prueba de instalación con MySQL/MariaDB descrita en el paso 5. La prueba requiere pdo_sqlite.
