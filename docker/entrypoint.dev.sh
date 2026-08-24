#!/bin/bash
set -e

echo "==> [dev] Preparando entorno de desarrollo..."

# 1. Dependencias de PHP (vendor vive en un volumen Docker aislado del host,
#    para no mezclar binarios nativos de Windows con los de Linux del contenedor)
if [ ! -f vendor/autoload.php ]; then
    echo "==> [dev] Instalando dependencias de Composer (primera vez, puede tardar)..."
    composer install --no-interaction
fi

# 2. Dependencias de Node (mismo motivo: node_modules en volumen aislado)
if [ ! -d node_modules/.bin ]; then
    echo "==> [dev] Instalando dependencias de npm (primera vez, puede tardar)..."
    npm install
fi

# 3. Archivo .env: se crea solo si no existe.
if [ ! -f .env ]; then
    echo "==> [dev] Creando .env desde .env.example..."
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=." .env; then
    echo "==> [dev] Generando APP_KEY..."
    php artisan key:generate --force
fi

# 3.b Forzar en el .env los valores de conexión que corresponden DENTRO de
#     la red de Docker. No basta con inyectarlos vía "environment:" en
#     docker-compose.yml: "php artisan serve" lanza un subproceso nuevo por
#     cada petición y no siempre hereda esas variables de forma confiable,
#     así que terminaba usando los valores viejos del .env (127.0.0.1:3307,
#     los del modo híbrido nativo). Reescribiéndolos aquí directamente se
#     garantiza que TODOS los procesos PHP (CLI, serve, queue:listen) usen
#     los mismos valores, sin depender de la herencia de entorno de cada uno.
set_env_var() {
    local key="$1" value="$2"
    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
}

echo "==> [dev] Sincronizando .env con la configuración de Docker..."
set_env_var "APP_ENV" "local"
set_env_var "APP_DEBUG" "true"
set_env_var "APP_URL" "http://localhost:8000"
set_env_var "DB_CONNECTION" "mysql"
set_env_var "DB_HOST" "${DB_HOST}"
set_env_var "DB_PORT" "${DB_PORT}"
set_env_var "DB_DATABASE" "${DB_DATABASE}"
set_env_var "DB_USERNAME" "${DB_USERNAME}"
set_env_var "DB_PASSWORD" "${DB_PASSWORD}"

# 4. Esperar a que MariaDB acepte conexiones antes de migrar
echo "==> [dev] Esperando a la base de datos (${DB_HOST}:${DB_PORT})..."
until mysqladmin ping -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" -p"${DB_PASSWORD}" --skip-ssl --silent 2>/dev/null; do
    sleep 1
done
echo "==> [dev] Base de datos disponible."

# 5. Migrar + sembrar. Es seguro correrlo en cada arranque: todos los
#    seeders del proyecto usan updateOrCreate/firstOrCreate, así que no
#    duplican datos ni fallan si ya se corrieron antes.
echo "==> [dev] Ejecutando migraciones y seeders..."
php artisan config:clear
php artisan migrate --seed --force

# 6. Levantar servidor + cola + logs (Pail) + Vite en paralelo.
#    Nota: dentro de Linux sí funciona Pail (a diferencia de Windows nativo,
#    donde la extensión pcntl que necesita no existe), así que aquí se
#    incluye el set completo de herramientas de desarrollo.
echo "==> [dev] Iniciando server, queue, logs y vite..."
exec npx concurrently -c "#93c5fd,#c4b5fd,#fb7185,#fdba74" \
    "php artisan serve --host=0.0.0.0 --port=8000" \
    "php artisan queue:listen --tries=1 --timeout=0" \
    "php artisan pail --timeout=0" \
    "npm run dev" \
    --names=server,queue,logs,vite --kill-others
