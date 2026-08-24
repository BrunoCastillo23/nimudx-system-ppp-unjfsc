<p align="center">
  <a href="https://github.com/martii-n/system-ppp-unjfsc">
    <img src="public/ins-unjfsc.png" alt="Logo UNJFSC" width="120" height="120">
  </a>
</p>

<h1 align="center">Sistema de Gestión de Prácticas Pre-Profesionales (UNJFSC)</h1>

<p align="center">
  ¡Bienvenido al repositorio oficial del Sistema de Gestión de Prácticas Pre-Profesionales para la Universidad Nacional José Faustino Sánchez Carrión! Este sistema está diseñado bajo una arquitectura robusta, modular y de alto rendimiento utilizando un stack moderno full-stack.
</p>

### Stack Tecnológico

* **Backend:** Laravel (PHP) ![version](https://img.shields.io/badge/version-12.0.0-red) ![version](https://img.shields.io/badge/version-8.2-blue)
* **Frontend:** React + Inertia.js ![version](https://img.shields.io/badge/version-19.2.0-blue)
* **Estilos y UI:** Tailwind CSS + Shadcn UI ![version](https://img.shields.io/badge/porcent-90%25-brightgreen)
* **Base de Datos:** MariaDB ![version](https://img.shields.io/badge/version-10.11-blue)
* **Entorno y Despliegue:** Docker & Docker Compose
* **Seguridad:** Cloudflare Proxy / Tunnels (Producción)

---

### Estructura Principal del Proyecto

```text
├── app/                  # Lógica del Backend (Controladores, Modelos, Middleware)
├── bootstrap/            # Configuración de inicialización de Laravel
├── config/               # Archivos de configuración del framework
├── database/             # Migraciones, Factories y Seeders (MariaDB)
├── docker/               # Archivos de configuración de entornos Docker (Nginx, PHP, etc.)
├── resources/
│   ├── js/               # Frontend en React + Inertia.js
│   │   ├── Components/   # Componentes reutilizables (Shadcn UI)
│   │   ├── Pages/        # Vistas y Páginas de la aplicación
│   │   └── app.jsx       # Punto de entrada de React
│   └── views/            # Plantilla raíz (root.blade.php)
├── routes/               # Rutas de la Aplicación (web.php, api.php)
├── docker-compose.yml    # Orquestación de contenedores (Dev/Prod)
└── vite.config.js        # Configuración del empaquetador Vite
```
---

### 1. Modo Desarrollo 100% Dockerizado (recomendado para el equipo)

Esta es la forma más rápida de empezar a trabajar en el proyecto sin instalar PHP, Composer ni Node en tu máquina — todo corre dentro de contenedores, con hot-reload real (los cambios que hagas en tu editor se reflejan al instante, tanto en PHP como en React).

#### Prerrequisitos
* Docker Desktop (con WSL2 backend si estás en Windows)

Eso es todo. No necesitas PHP, Composer ni Node instalados localmente para este modo.

#### Pasos

1. **Clonar el repositorio**
    ```bash
    git clone https://github.com/martii-n/system-ppp-unjfsc.git
    cd system-ppp-unjfsc
    ```

2. **Levantar todo el entorno de desarrollo**
    ```bash
    docker compose --profile dev up --build
    ```
    La primera vez tardará varios minutos (construye la imagen, instala Composer y npm dentro del contenedor). Las siguientes veces arranca en segundos, porque `vendor/` y `node_modules/` quedan guardados en volúmenes de Docker que persisten entre reinicios.

    Este comando levanta automáticamente:
    - `db-dev`: MariaDB (puerto `3307` hacia el host)
    - `app-dev`: Laravel + Vite + cola + Pail (logs en vivo), con hot-reload

    El propio contenedor `app-dev` se encarga de: crear el `.env` si no existe, generar `APP_KEY`, esperar a que la base de datos esté lista, correr `migrate --seed` (es seguro repetirlo en cada arranque, no duplica datos), e iniciar todos los procesos. No hace falta que edites nada a mano.

3. **Abrir el proyecto**
    ```
    http://localhost:8000
    ```
    Credenciales del usuario administrador creado automáticamente por el seeder:
    - **Email:** `admin@unjfsc.edu.pe`
    - **Password:** `password`

4. **Ver los logs** (server, queue, vite y Pail corren todos dentro del mismo contenedor)
    ```bash
    docker compose logs -f app-dev
    ```

5. **Detener el entorno** (los datos de la base de datos y las dependencias instaladas se conservan)
    ```bash
    docker compose --profile dev down
    ```
    ⚠️ No agregues `-v` a menos que quieras borrar también la base de datos y las dependencias instaladas (`docker compose --profile dev down -v`).

6. **Si cambias el `Dockerfile.dev` o necesitas reconstruir la imagen**
    ```bash
    docker compose --profile dev up --build
    ```

7. **Si necesitas correr un comando puntual dentro del contenedor** (por ejemplo, un nuevo `artisan make:`, o instalar un paquete de Composer/npm)
    ```bash
    docker exec -it app-dev php artisan make:controller EjemploController
    docker exec -it app-dev composer require paquete/nuevo
    docker exec -it app-dev npm install paquete-nuevo
    ```

---

### 2. Modo Desarrollo Híbrido (Docker solo para la BD)

Alternativa si prefieres depurar con tu editor conectado directamente al proceso de PHP (breakpoints nativos, Xdebug, etc.) en vez de dentro de un contenedor. La base de datos corre aislada en Docker, mientras que Laravel y React se ejecutan de forma nativa en tu máquina local.

### Prerrequisitos
Asegúrate de tener instalado en tu sistema local:
* Docker & Docker Compose
* PHP 8.4+ & Composer
* Node.js (v18+ recomendado) & NPM

#### Pasos para Inicializar en Local

1. **Clonar el repositorio (rama por defecto/main):**
   ```bash
   git clone https://github.com/martii-n/system-ppp-unjfsc.git
   cd system-ppp-unjfsc
2. **Configurar el archivo de entorno local**
    ```bash
    cp .env.example .env
    ```
    Nota crítica: Abre tu .env y configura DB_PORT=3307 para conectar correctamente con el mapeo del contenedor externo de desarrollo.
3. **Instalar dependencias del backend e iniciar la Base de Datos**
    ```bash
    # Instalar paquetes de Composer locales
    composer install
    
    # Generar la clave única de la aplicación
    php artisan key:generate
    
    # Levantar el contenedor de MariaDB en segundo plano
    docker compose --profile dev up -d
    ```
4. **Ejecutar Migraciones y Poblado de Datos (Seeds)**
    ```bash
    php artisan migrate --seed
    ```
    Nota: Si el comando falla indicando que busca el puerto 3306, limpia la caché de configuración con **php artisan config:clear** o fuerza el puerto ejecutando: DB_PORT=3307 **php artisan migrate --seed**.
5. **Instalar dependencias del frontend**
    ```bash
    npm install
    ```
6. **Ejecución del Entorno de Desarrollo**
    Para comenzar a trabajar, necesitas levantar tanto el servidor de PHP como el compilador de Vite.
    - **Windows** (Pail/`pcntl` no está disponible de forma nativa en Windows, por eso hay un script separado sin él):
      ```bash
      composer run dev:windows
      ```
    - **Mac/Linux:**
      ```bash
      composer run dev
      ```

    http://localhost:8000

---

### 3. Modo Producción (VPS + Docker Completo)
El entorno de producción corre de forma 100% contenerizada dentro del VPS. Todo el tráfico web externo está completamente blindado y anonimizado utilizando Cloudflare Tunnels, eliminando la necesidad de exponer puertos públicos del servidor al internet.

### Prerrequisitos en el VPS
* Docker & Docker Compose instalado de forma nativa.
* Cuenta de Cloudflare con los Nameservers delegados correctamente.

#### Pasos para Inicializar en Local

1. **Clonar la rama de producción directamente en el VPS**
    ```bash
    git clone -b production https://github.com/martii-n/system-ppp-unjfsc.git
    cd system-ppp-unjfsc
    ```
2. **Configurar el entorno de producción**
    ```bash
    cp .env.example .env
    nano .env
    ```
    Asegúrate de definir variables seguras para la base de datos de producción (db_system_ppp_prod).
3. **Levantar todo el Stack Productivo**
    Levanta de forma aislada los contenedores de producción (nginx, app-prod, db-prod) limitando sus recursos para proteger la RAM del VPS:
    ```bash
    docker compose --profile prod up -d
4. **Ejecutar Migraciones Estructurales**
    Corre las migraciones de forma interna y segura dentro del contenedor productivo app:
    ```bash
    docker exec -it app-prod php artisan migrate --force
    ```
5. **Optimizar el Rendimiento de Laravel**
    Obliga a Laravel a precargar las configuraciones, rutas y layouts en memoria para acelerar la velocidad de carga de las peticiones de Inertia:
    ```bash
    docker exec -it app-prod php artisan optimize
    ```
    El sistema estará listo y securizado, respondiendo directamente de forma cifra a través de 
    https://sistemappp.kerpun.com

## Licencia

Este proyecto está bajo la licencia **Attribution-NonCommercial-NoDerivatives 4.0 International (CC BY-NC-ND 4.0)**.

A efectos prácticos, esto significa que:
* **Permitido:** Puedes ver, descargar, estudiar y compartir el código con fines académicos y de aprendizaje.
* **Atribución:** Se debe dar el crédito correspondiente de manera adecuada, proporcionando un enlace a este repositorio.
* **No Comercial:** No puedes utilizar este proyecto, su código o sus componentes para fines comerciales o lucrativos.
* **Sin Derivadas:** Si remezclas, transformas o creas a partir de este material, no puedes distribuir el material modificado.

Para más detalles, puedes revisar el archivo [LICENSE](LICENSE) o visitar [Creative Commons](https://creativecommons.org/licenses/by-nc-nd/4.0/deed.es).
