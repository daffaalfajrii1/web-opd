#!/usr/bin/env bash
set -Eeuo pipefail

# ============================================================
# Laravel Deployment Manager - Worker Node02
# Target worker : 10.90.120.10
# App Nginx port: 8088 (port 80 tetap untuk Docker/Geoportal)
# Reverse proxy : 10.90.120.6 -> http://10.90.120.10:8088
#
# PHP multi-version:
#   - Site lama bisa tetap 8.2
#   - App baru (mis. anjab) pilih 8.3 saat init
# ============================================================

BASE="/opt/deploy-node02"
APPS="$BASE/apps"
PROXY_SNIPPETS="$BASE/proxy-snippets"
WORKER_IP="10.90.120.10"
APP_PORT="8088"
DEFAULT_PHP_VERSION="8.2"
DEFAULT_DEPLOY_USER="daffa"

mkdir -p "$APPS" "$PROXY_SNIPPETS"

info(){ printf '\033[1;34m[INFO]\033[0m %s\n' "$*"; }
ok(){ printf '\033[1;32m[OK]\033[0m %s\n' "$*"; }
warn(){ printf '\033[1;33m[WARN]\033[0m %s\n' "$*"; }
die(){ printf '\033[1;31m[ERROR]\033[0m %s\n' "$*" >&2; exit 1; }

require_root(){ [[ ${EUID:-$(id -u)} -eq 0 ]] || die "Jalankan dengan sudo/root."; }
needcmd(){ command -v "$1" >/dev/null 2>&1 || die "Command '$1' belum terpasang."; }
slug_ok(){ [[ "$1" =~ ^[a-z0-9][a-z0-9._-]*$ ]] || die "Slug tidak valid: $1"; }

php_bin(){
    local ver="${1:-$DEFAULT_PHP_VERSION}"
    if command -v "php${ver}" >/dev/null 2>&1; then
        command -v "php${ver}"
    elif [[ -x "/usr/bin/php${ver}" ]]; then
        echo "/usr/bin/php${ver}"
    else
        die "PHP ${ver} belum terpasang (php${ver} tidak ditemukan)."
    fi
}

php_fpm_sock(){
    local ver="${1:-$DEFAULT_PHP_VERSION}"
    local sock="/run/php/php${ver}-fpm.sock"
    [[ -S "$sock" ]] || die "Socket PHP-FPM tidak ditemukan: $sock (pasang/aktifkan php${ver}-fpm)."
    echo "$sock"
}

php_fpm_service(){
    local ver="${1:-$DEFAULT_PHP_VERSION}"
    echo "php${ver}-fpm"
}

validate_php_version(){
    local ver="$1"
    [[ "$ver" =~ ^8\.(2|3)$ ]] || die "PHP_VERSION tidak didukung: $ver (pilih 8.2 atau 8.3)."
}

ensure_php_ready(){
    local ver="$1"
    validate_php_version "$ver"

    local bin
    bin="$(php_bin "$ver")"
    needcmd composer

    local sock service
    sock="$(php_fpm_sock "$ver")"
    service="$(php_fpm_service "$ver")"

    if ! systemctl is-active --quiet "$service"; then
        warn "Service $service belum aktif, mencoba start..."
        systemctl enable --now "$service" || die "Gagal mengaktifkan $service."
    fi

    info "PHP runtime: $($bin -v | head -n1)"
    info "PHP-FPM sock: $sock"
}

run_php(){
    local bin
    bin="$(php_bin "${PHP_VERSION:-$DEFAULT_PHP_VERSION}")"
    runusercmd "$bin" "$@"
}

run_composer(){
    local bin
    bin="$(php_bin "${PHP_VERSION:-$DEFAULT_PHP_VERSION}")"
    # Pastikan composer dijalankan dengan binary PHP yang dipilih
    runusercmd "$bin" "$(command -v composer)" "$@"
}

get_deploy_user(){
    if [[ -n "${SUDO_USER:-}" && "${SUDO_USER}" != "root" ]]; then
        DEPLOY_USER="$SUDO_USER"
    else
        DEPLOY_USER="$DEFAULT_DEPLOY_USER"
    fi

    id "$DEPLOY_USER" >/dev/null 2>&1 || die "User deployment '$DEPLOY_USER' tidak ditemukan."
    DEPLOY_HOME="$(getent passwd "$DEPLOY_USER" | cut -d: -f6)"
    [[ -n "$DEPLOY_HOME" && -d "$DEPLOY_HOME" ]] || die "Home user '$DEPLOY_USER' tidak ditemukan."
}

runusercmd(){
    runuser -u "$DEPLOY_USER" -- env HOME="$DEPLOY_HOME" "$@"
}

saveconf(){
    local file="$APPS/$APP_SLUG.conf"
    {
        printf 'APP_SLUG=%q\n' "$APP_SLUG"
        printf 'APP_NAME=%q\n' "$APP_NAME"
        printf 'APP_FOLDER=%q\n' "$APP_FOLDER"
        printf 'DOMAIN=%q\n' "$DOMAIN"
        printf 'APP_DIR=%q\n' "$APP_DIR"
        printf 'SOURCE_TYPE=%q\n' "$SOURCE_TYPE"
        printf 'GIT_REPO=%q\n' "${GIT_REPO:-}"
        printf 'GIT_BRANCH=%q\n' "${GIT_BRANCH:-main}"
        printf 'DB_NAME=%q\n' "$DB_NAME"
        printf 'DB_USER=%q\n' "$DB_USER"
        printf 'FORCE_HTTPS=%q\n' "$FORCE_HTTPS"
        printf 'PHP_VERSION=%q\n' "${PHP_VERSION:-$DEFAULT_PHP_VERSION}"
    } > "$file"
    chmod 600 "$file"
}

loadconf(){
    slug_ok "$1"
    [[ -f "$APPS/$1.conf" ]] || die "Aplikasi '$1' belum terdaftar."
    # shellcheck disable=SC1090
    source "$APPS/$1.conf"
    PHP_VERSION="${PHP_VERSION:-$DEFAULT_PHP_VERSION}"
    validate_php_version "$PHP_VERSION"
}

setup_github_deploy_key(){
    local slug="$1"
    local original_repo="$2"

    get_deploy_user

    local key_slug="${slug//[^A-Za-z0-9._-]/_}"
    local ssh_dir="$DEPLOY_HOME/.ssh"
    local key_file="$ssh_dir/deploy_${key_slug}_ed25519"
    local host_alias="github-${key_slug}"
    local ssh_config="$ssh_dir/config"
    local repo_path=""
    local aliased_repo=""

    case "$original_repo" in
        git@github.com:*) repo_path="${original_repo#git@github.com:}" ;;
        https://github.com/*) repo_path="${original_repo#https://github.com/}" ;;
        *)
            die "Format repo private tidak dikenali. Gunakan git@github.com:user/repo.git atau https://github.com/user/repo.git"
            ;;
    esac

    install -d -m 700 -o "$DEPLOY_USER" -g "$DEPLOY_USER" "$ssh_dir"

    if [[ ! -f "$key_file" ]]; then
        info "Membuat Deploy Key khusus aplikasi '$slug'..."
        runusercmd ssh-keygen \
            -t ed25519 \
            -C "deploy-${slug}@$(hostname)" \
            -f "$key_file" \
            -N ""
    else
        warn "Deploy Key sudah ada: $key_file"
    fi

    chown "$DEPLOY_USER:$DEPLOY_USER" "$key_file" "${key_file}.pub"
    chmod 600 "$key_file"
    chmod 644 "${key_file}.pub"

    touch "$ssh_config"
    chown "$DEPLOY_USER:$DEPLOY_USER" "$ssh_config"
    chmod 600 "$ssh_config"

    sed -i "/^# BEGIN DEPLOY ${key_slug}$/,/^# END DEPLOY ${key_slug}$/d" "$ssh_config"

    cat >> "$ssh_config" <<CFG

# BEGIN DEPLOY ${key_slug}
Host ${host_alias}
    HostName github.com
    User git
    IdentityFile ${key_file}
    IdentitiesOnly yes
    StrictHostKeyChecking accept-new
# END DEPLOY ${key_slug}
CFG

    chown "$DEPLOY_USER:$DEPLOY_USER" "$ssh_config"
    chmod 600 "$ssh_config"

    aliased_repo="git@${host_alias}:${repo_path}"

    echo
    echo "============================================================"
    echo " GITHUB DEPLOY KEY - $slug"
    echo "============================================================"
    echo
    cat "${key_file}.pub"
    echo
    echo "Tambahkan PUBLIC KEY di GitHub:"
    echo "Repository -> Settings -> Deploy keys -> Add deploy key"
    echo "Title: ${slug} - $(hostname)"
    echo "JANGAN centang 'Allow write access'."
    echo

    while true; do
        read -r -p "Sudah ditambahkan? ENTER=test, q=batal: " answer
        [[ "$answer" =~ ^[Qq]$ ]] && die "Setup private repo dibatalkan."

        info "Menguji akses private repository..."
        if runusercmd git ls-remote "$aliased_repo" HEAD >/dev/null 2>&1; then
            ok "Deploy Key berhasil mengakses repository."
            break
        fi
        warn "Belum bisa mengakses repository. Cek Deploy Key di GitHub lalu coba lagi."
    done

    GIT_REPO="$aliased_repo"
}

validate_db_ident(){
    [[ "$1" =~ ^[A-Za-z0-9_]+$ ]] || die "Nama database/user DB hanya boleh huruf, angka, underscore: $1"
}

create_db(){
    validate_db_ident "$DB_NAME"
    validate_db_ident "$DB_USER"

    if [[ -z "${DB_PASS:-}" ]]; then
        DB_PASS="$(openssl rand -base64 36 | tr -dc 'A-Za-z0-9' | head -c 28)"
    fi

    info "Membuat/menyiapkan database '$DB_NAME'..."
    mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL
}

set_env_line(){
    local key="$1" value="$2" file="$3"
    if grep -q "^${key}=" "$file"; then
        sed -i "s|^${key}=.*|${key}=${value}|" "$file"
    else
        printf '%s=%s\n' "$key" "$value" >> "$file"
    fi
}

prepare_env(){
    local envfile="$APP_DIR/.env"

    if [[ ! -f "$envfile" ]]; then
        [[ -f "$APP_DIR/.env.example" ]] || die ".env.example tidak ditemukan."
        cp "$APP_DIR/.env.example" "$envfile"
    fi

    local app_name_escaped="${APP_NAME//\"/\\\"}"
    set_env_line APP_NAME "\"${app_name_escaped}\"" "$envfile"
    set_env_line APP_ENV "production" "$envfile"
    set_env_line APP_DEBUG "false" "$envfile"
    set_env_line APP_URL "https://${DOMAIN}" "$envfile"
    set_env_line ASSET_URL "https://${DOMAIN}" "$envfile"
    set_env_line DB_CONNECTION "mysql" "$envfile"
    set_env_line DB_HOST "127.0.0.1" "$envfile"
    set_env_line DB_PORT "3306" "$envfile"
    set_env_line DB_DATABASE "$DB_NAME" "$envfile"
    set_env_line DB_USERNAME "$DB_USER" "$envfile"
    set_env_line DB_PASSWORD "$DB_PASS" "$envfile"
}

permissions(){
    get_deploy_user
    chown -R "$DEPLOY_USER:www-data" "$APP_DIR"

    mkdir -p \
        "$APP_DIR/storage/framework/cache/data" \
        "$APP_DIR/storage/framework/sessions" \
        "$APP_DIR/storage/framework/views" \
        "$APP_DIR/bootstrap/cache"

    chown -R "$DEPLOY_USER:www-data" "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
    find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type d -exec chmod 2775 {} \;
    find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type f -exec chmod 664 {} \;

    if [[ -f "$APP_DIR/.env" ]]; then
        chown "$DEPLOY_USER:www-data" "$APP_DIR/.env"
        chmod 640 "$APP_DIR/.env"
    fi
}

composer_install(){
    [[ -f "$APP_DIR/composer.json" ]] || return 0
    info "Composer install (PHP ${PHP_VERSION})..."
    (
        cd "$APP_DIR"
        run_composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    )
}

npm_build(){
    [[ -f "$APP_DIR/package.json" ]] || return 0
    needcmd node
    needcmd npm

    # Laravel Mix/webpack ada di devDependencies. Jangan biarkan
    # NODE_ENV=production / npm production mode meng-omit paket itu.
    info "NPM install (include devDependencies)..."
    (
        cd "$APP_DIR"

        # Bersihkan install setengah jadi yang sering bikin MODULE_NOT_FOUND
        if [[ -d node_modules/webpack && ! -d node_modules/webpack-cli && ! -d node_modules/laravel-mix ]]; then
            warn "node_modules webpack tidak lengkap — dihapus lalu install ulang."
            rm -rf node_modules
        fi

        # Paksa install devDeps walau environment production
        if [[ -f package-lock.json ]]; then
            runusercmd env NODE_ENV=development NPM_CONFIG_PRODUCTION=false npm ci --include=dev
        else
            runusercmd env NODE_ENV=development NPM_CONFIG_PRODUCTION=false npm install --include=dev --no-audit --no-fund
        fi

        # Urutan: build -> production -> prod (Laravel Mix lama)
        local build_script=""
        if runusercmd node -e "const p=require('./package.json'); process.exit(p.scripts && p.scripts.build ? 0 : 1)"; then
            build_script="build"
        elif runusercmd node -e "const p=require('./package.json'); process.exit(p.scripts && p.scripts.production ? 0 : 1)"; then
            build_script="production"
        elif runusercmd node -e "const p=require('./package.json'); process.exit(p.scripts && p.scripts.prod ? 0 : 1)"; then
            build_script="prod"
        fi

        if [[ -z "$build_script" ]]; then
            warn "Tidak ada script npm build/production/prod — dilewati."
            return 0
        fi

        info "NPM ${build_script}..."
        if runusercmd env NODE_ENV=development npm run "$build_script"; then
            ok "NPM ${build_script} selesai."
            return 0
        fi

        # PHP-only update jangan gagal total jika aset frontend sudah ada
        if [[ -f "$APP_DIR/public/mix-manifest.json" || -f "$APP_DIR/public/js/app.js" || -f "$APP_DIR/public/css/app.css" ]]; then
            warn "NPM ${build_script} gagal, tapi aset public/ sudah ada — update dilanjutkan."
            warn "Perbaiki frontend deps (webpack/laravel-mix) lalu jalankan ulang npm di app dir jika perlu."
            return 0
        fi

        die "NPM ${build_script} gagal dan aset frontend belum tersedia di public/."
    )
}

artisan_prepare(){
    [[ -f "$APP_DIR/artisan" ]] || die "File artisan tidak ditemukan di $APP_DIR"
    (
        cd "$APP_DIR"
        if ! grep -qE '^APP_KEY=base64:.+' .env 2>/dev/null; then
            run_php artisan key:generate --force
        fi
        run_php artisan migrate --force
        run_php artisan storage:link >/dev/null 2>&1 || true
        run_php artisan optimize:clear
        run_php artisan optimize
    )
}

nginx_app_config(){
    local site="/etc/nginx/sites-available/$APP_SLUG"
    local sock
    sock="$(php_fpm_sock "$PHP_VERSION")"

    cat > "$site" <<NGINX
server {
    listen ${APP_PORT};
    listen [::]:${APP_PORT};
    server_name ${DOMAIN};

    client_max_body_size 50M;

    root ${APP_DIR}/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:${sock};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        fastcgi_param HTTP_X_FORWARDED_PROTO \$http_x_forwarded_proto;
        fastcgi_param HTTP_X_FORWARDED_FOR \$http_x_forwarded_for;
        fastcgi_param HTTP_X_FORWARDED_HOST \$http_x_forwarded_host;
        include fastcgi_params;
    }

    location /storage/ {
        alias ${APP_DIR}/storage/app/public/;
        access_log off;
    }

    location ~ /\\.ht {
        deny all;
    }
}
NGINX

    ln -sfn "$site" "/etc/nginx/sites-enabled/$APP_SLUG"
    nginx -t
    systemctl reload nginx
}

proxy_template(){
    local f="$PROXY_SNIPPETS/$APP_SLUG.conf"
    cat > "$f" <<PROXY
# Reverse proxy template for ${DOMAIN}
# Install on reverse proxy 10.90.120.6

server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ${DOMAIN};

    include /etc/nginx/snippets/ssl-rejanglebongkab.conf;
    client_max_body_size 50M;

    location / {
        proxy_pass http://${WORKER_IP}:${APP_PORT};
        proxy_http_version 1.1;

        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
        proxy_set_header X-Forwarded-Host \$host;
        proxy_set_header X-Forwarded-Port 443;
    }
}
PROXY
    chmod 640 "$f"
}

clone_git(){
    get_deploy_user
    mkdir -p "$APP_DIR"
    chown "$DEPLOY_USER:www-data" "$APP_DIR"
    [[ -z "$(find "$APP_DIR" -mindepth 1 -maxdepth 1 -print -quit 2>/dev/null)" ]] || die "$APP_DIR tidak kosong. Untuk aplikasi existing gunakan attach/manual registry, bukan init."
    info "Clone repository..."
    ( cd "$APP_DIR"; runusercmd git clone --branch "$GIT_BRANCH" --single-branch "$GIT_REPO" . )
}

zip_init(){
    local zip="$1"
    get_deploy_user
    [[ -f "$zip" ]] || die "ZIP tidak ditemukan: $zip"
    mkdir -p "$APP_DIR"
    [[ -z "$(find "$APP_DIR" -mindepth 1 -maxdepth 1 -print -quit 2>/dev/null)" ]] || die "$APP_DIR tidak kosong."
    unzip -q "$zip" -d "$APP_DIR"
    chown -R "$DEPLOY_USER:www-data" "$APP_DIR"
}

update_git(){
    get_deploy_user
    [[ -d "$APP_DIR/.git" ]] || die "$APP_DIR belum merupakan Git repository."

    info "Fetch $GIT_BRANCH..."
    ( cd "$APP_DIR"; runusercmd git fetch origin "$GIT_BRANCH" )

    ( cd "$APP_DIR"; run_php artisan down --retry=30 >/dev/null 2>&1 || true )

    info "Sinkron source ke origin/$GIT_BRANCH..."
    ( cd "$APP_DIR"; runusercmd git reset --hard "origin/$GIT_BRANCH" )

    composer_install
    npm_build
    artisan_prepare
    permissions

    ( cd "$APP_DIR"; run_php artisan up >/dev/null 2>&1 || true )
}

update_zip(){
    local zip="$1"
    [[ -f "$zip" ]] || die "ZIP tidak ditemukan: $zip"
    needcmd rsync
    needcmd unzip

    local tmp
    tmp="$(mktemp -d)"
    trap 'rm -rf "$tmp"' RETURN
    unzip -q "$zip" -d "$tmp"

    local src="$tmp"
    local count
    count="$(find "$tmp" -mindepth 1 -maxdepth 1 -type d | wc -l)"
    if [[ "$count" -eq 1 && -z "$(find "$tmp" -mindepth 1 -maxdepth 1 -type f -print -quit)" ]]; then
        src="$(find "$tmp" -mindepth 1 -maxdepth 1 -type d | head -n1)"
    fi

    ( cd "$APP_DIR"; run_php artisan down --retry=30 >/dev/null 2>&1 || true )

    rsync -a --delete \
        --exclude='.env' \
        --exclude='.git/' \
        --exclude='storage/' \
        --exclude='public/storage' \
        "$src/" "$APP_DIR/"

    composer_install
    npm_build
    artisan_prepare
    permissions
    ( cd "$APP_DIR"; run_php artisan up >/dev/null 2>&1 || true )
}

ask_php_version(){
    echo
    echo "Pilih versi PHP untuk aplikasi ini:"
    echo "  1) PHP 8.2  (default / site lama)"
    echo "  2) PHP 8.3  (untuk project yang wajib >= 8.3.36)"
    read -r -p "Pilih [1-2, default 1]: " PHP_CHOICE
    case "${PHP_CHOICE:-1}" in
        1) PHP_VERSION="8.2" ;;
        2) PHP_VERSION="8.3" ;;
        *) die "Pilihan PHP tidak valid." ;;
    esac
    ensure_php_ready "$PHP_VERSION"
    ok "Aplikasi ini akan memakai PHP ${PHP_VERSION}"
}

cmd_init(){
    require_root
    get_deploy_user
    needcmd git
    needcmd composer
    needcmd nginx
    needcmd mysql
    needcmd openssl

    echo "=== INIT LARAVEL - NODE02 ${WORKER_IP}:${APP_PORT} ==="
    echo

    read -r -p "Slug aplikasi (contoh: sipedas): " APP_SLUG
    slug_ok "$APP_SLUG"
    [[ ! -f "$APPS/$APP_SLUG.conf" ]] || die "Slug '$APP_SLUG' sudah terdaftar."

    read -r -p "Nama aplikasi: " APP_NAME
    read -r -p "Folder di /var/www [$APP_SLUG]: " APP_FOLDER
    APP_FOLDER="${APP_FOLDER:-$APP_SLUG}"
    [[ "$APP_FOLDER" != *"/"* && "$APP_FOLDER" != *".."* ]] || die "APP_FOLDER tidak valid."
    APP_DIR="/var/www/$APP_FOLDER"
    [[ ! -e "$APP_DIR" ]] || die "$APP_DIR sudah ada. Untuk aplikasi existing jangan gunakan init."

    read -r -p "Domain (tanpa https://): " DOMAIN
    [[ "$DOMAIN" =~ ^[A-Za-z0-9.-]+$ ]] || die "Domain tidak valid."

    ask_php_version

    echo
    echo "Sumber aplikasi:"
    echo "  1) Git Public HTTPS"
    echo "  2) Private GitHub SSH Deploy Key"
    echo "  3) ZIP"
    read -r -p "Pilih [1-3]: " SOURCE_CHOICE

    GIT_REPO=""
    GIT_BRANCH="main"
    ZIP_PATH=""

    case "$SOURCE_CHOICE" in
        1)
            SOURCE_TYPE="git-public"
            read -r -p "Repo HTTPS: " GIT_REPO
            read -r -p "Branch [main]: " GIT_BRANCH
            GIT_BRANCH="${GIT_BRANCH:-main}"
            ;;
        2)
            SOURCE_TYPE="git-private"
            read -r -p "Repo GitHub private (git@github.com:user/repo.git): " ORIGINAL_GIT_REPO
            read -r -p "Branch [main]: " GIT_BRANCH
            GIT_BRANCH="${GIT_BRANCH:-main}"
            setup_github_deploy_key "$APP_SLUG" "$ORIGINAL_GIT_REPO"
            ;;
        3)
            SOURCE_TYPE="zip"
            needcmd unzip
            read -r -p "Path ZIP [/tmp/$APP_SLUG.zip]: " ZIP_PATH
            ZIP_PATH="${ZIP_PATH:-/tmp/$APP_SLUG.zip}"
            [[ -f "$ZIP_PATH" ]] || die "ZIP tidak ada: $ZIP_PATH"
            ;;
        *) die "Pilihan sumber tidak valid." ;;
    esac

    echo
    read -r -p "Database [$APP_SLUG]: " DB_NAME
    DB_NAME="${DB_NAME:-${APP_SLUG//-/_}}"
    read -r -p "DB user [${APP_SLUG//-/_}_user]: " DB_USER
    DB_USER="${DB_USER:-${APP_SLUG//-/_}_user}"
    read -r -s -p "DB password [ENTER=generate otomatis]: " DB_PASS
    echo

    FORCE_HTTPS=true

    create_db

    if [[ "$SOURCE_TYPE" == "zip" ]]; then
        zip_init "$ZIP_PATH"
    else
        clone_git
    fi

    prepare_env
    permissions
    composer_install
    npm_build
    artisan_prepare
    permissions
    nginx_app_config
    proxy_template
    saveconf

    echo
    ok "Deployment init selesai."
    echo "APP      : $APP_NAME"
    echo "DOMAIN   : $DOMAIN"
    echo "PATH     : $APP_DIR"
    echo "PHP      : ${PHP_VERSION}"
    echo "BACKEND  : http://${WORKER_IP}:${APP_PORT}"
    echo "REGISTRY : $APPS/$APP_SLUG.conf"
    echo "PROXY    : $PROXY_SNIPPETS/$APP_SLUG.conf"
    echo
    echo "Reverse proxy 10.90.120.6 harus proxy_pass ke http://${WORKER_IP}:${APP_PORT}"
}

cmd_update(){
    require_root
    [[ $# -ge 1 ]] || die "Usage: deploy-laravel-node02 update <slug> [zip-path]"
    loadconf "$1"
    get_deploy_user
    ensure_php_ready "$PHP_VERSION"
    needcmd composer

    case "$SOURCE_TYPE" in
        git-public|git-private)
            needcmd git
            if [[ "$SOURCE_TYPE" == "git-private" ]]; then
                runusercmd git ls-remote "$GIT_REPO" HEAD >/dev/null 2>&1 || die "Deploy Key/private repo bermasalah."
            fi
            update_git
            ;;
        zip)
            [[ $# -ge 2 ]] || die "Untuk source ZIP: update <slug> /tmp/app.zip"
            update_zip "$2"
            ;;
        *) die "SOURCE_TYPE invalid: $SOURCE_TYPE" ;;
    esac

    nginx_app_config
    proxy_template
    ok "Update '$APP_SLUG' selesai (PHP ${PHP_VERSION})."
}

cmd_set_php(){
    require_root
    [[ $# -ge 2 ]] || die "Usage: deploy-laravel-node02 set-php <slug> <8.2|8.3>"
    loadconf "$1"
    PHP_VERSION="$2"
    ensure_php_ready "$PHP_VERSION"
    saveconf
    nginx_app_config
    ok "PHP '$APP_SLUG' diganti ke ${PHP_VERSION}."
    echo "Jalankan update jika perlu rebuild vendor dengan PHP baru:"
    echo "  sudo deploy-laravel-node02 update $APP_SLUG"
}

cmd_register(){
    require_root
    get_deploy_user
    needcmd git
    needcmd nginx

    echo "=== REGISTER APP EXISTING (tanpa clone ulang) ==="
    echo "Pakai ini jika init terhenti tapi folder /var/www sudah ada."
    echo

    read -r -p "Slug aplikasi (contoh: simanjabeb): " APP_SLUG
    slug_ok "$APP_SLUG"
    [[ ! -f "$APPS/$APP_SLUG.conf" ]] || die "Slug '$APP_SLUG' sudah terdaftar. Hapus conf dulu jika ingin register ulang."

    read -r -p "Nama aplikasi [$APP_SLUG]: " APP_NAME
    APP_NAME="${APP_NAME:-$APP_SLUG}"

    read -r -p "Folder di /var/www [$APP_SLUG]: " APP_FOLDER
    APP_FOLDER="${APP_FOLDER:-$APP_SLUG}"
    [[ "$APP_FOLDER" != *"/"* && "$APP_FOLDER" != *".."* ]] || die "APP_FOLDER tidak valid."
    APP_DIR="/var/www/$APP_FOLDER"
    [[ -d "$APP_DIR" ]] || die "Folder tidak ditemukan: $APP_DIR"
    [[ -f "$APP_DIR/artisan" ]] || die "Bukan Laravel app (artisan tidak ada): $APP_DIR"

    read -r -p "Domain (tanpa https://): " DOMAIN
    [[ "$DOMAIN" =~ ^[A-Za-z0-9.-]+$ ]] || die "Domain tidak valid."

    ask_php_version

    echo
    echo "Sumber aplikasi:"
    echo "  1) Git Public HTTPS"
    echo "  2) Private GitHub SSH Deploy Key"
    echo "  3) Manual/ZIP (tanpa git update)"
    read -r -p "Pilih [1-3]: " SOURCE_CHOICE

    GIT_REPO=""
    GIT_BRANCH="main"

    case "$SOURCE_CHOICE" in
        1)
            SOURCE_TYPE="git-public"
            if [[ -d "$APP_DIR/.git" ]]; then
                CURRENT_REMOTE="$(cd "$APP_DIR" && runusercmd git remote get-url origin 2>/dev/null || true)"
                read -r -p "Repo HTTPS [${CURRENT_REMOTE:-wajib isi}]: " GIT_REPO
                GIT_REPO="${GIT_REPO:-$CURRENT_REMOTE}"
            else
                read -r -p "Repo HTTPS: " GIT_REPO
            fi
            [[ -n "$GIT_REPO" ]] || die "GIT_REPO wajib diisi."
            read -r -p "Branch [main]: " GIT_BRANCH
            GIT_BRANCH="${GIT_BRANCH:-main}"
            ;;
        2)
            SOURCE_TYPE="git-private"
            CURRENT_REMOTE=""
            if [[ -d "$APP_DIR/.git" ]]; then
                CURRENT_REMOTE="$(cd "$APP_DIR" && runusercmd git remote get-url origin 2>/dev/null || true)"
            fi
            read -r -p "Repo GitHub private [${CURRENT_REMOTE:-git@github.com:user/repo.git}]: " ORIGINAL_GIT_REPO
            ORIGINAL_GIT_REPO="${ORIGINAL_GIT_REPO:-$CURRENT_REMOTE}"
            [[ -n "$ORIGINAL_GIT_REPO" ]] || die "Repo private wajib diisi."
            read -r -p "Branch [main]: " GIT_BRANCH
            GIT_BRANCH="${GIT_BRANCH:-main}"
            setup_github_deploy_key "$APP_SLUG" "$ORIGINAL_GIT_REPO"
            if [[ -d "$APP_DIR/.git" ]]; then
                info "Mengatur remote origin ke Deploy Key alias..."
                ( cd "$APP_DIR"; runusercmd git remote set-url origin "$GIT_REPO" )
            fi
            ;;
        3)
            SOURCE_TYPE="zip"
            GIT_REPO=""
            GIT_BRANCH="main"
            ;;
        *) die "Pilihan sumber tidak valid." ;;
    esac

    # Ambil DB dari .env jika ada
    DB_NAME="${APP_SLUG//-/_}"
    DB_USER="${APP_SLUG//-/_}_user"
    if [[ -f "$APP_DIR/.env" ]]; then
        ENV_DB_NAME="$(grep -E '^DB_DATABASE=' "$APP_DIR/.env" | head -n1 | cut -d= -f2- | tr -d '"' || true)"
        ENV_DB_USER="$(grep -E '^DB_USERNAME=' "$APP_DIR/.env" | head -n1 | cut -d= -f2- | tr -d '"' || true)"
        [[ -n "$ENV_DB_NAME" ]] && DB_NAME="$ENV_DB_NAME"
        [[ -n "$ENV_DB_USER" ]] && DB_USER="$ENV_DB_USER"
    fi
    read -r -p "Database [$DB_NAME]: " DB_NAME_INPUT
    DB_NAME="${DB_NAME_INPUT:-$DB_NAME}"
    read -r -p "DB user [$DB_USER]: " DB_USER_INPUT
    DB_USER="${DB_USER_INPUT:-$DB_USER}"

    FORCE_HTTPS=true

    permissions
    nginx_app_config
    proxy_template
    saveconf

    echo
    ok "Aplikasi '$APP_SLUG' berhasil didaftarkan."
    echo "APP      : $APP_NAME"
    echo "DOMAIN   : $DOMAIN"
    echo "PATH     : $APP_DIR"
    echo "PHP      : $PHP_VERSION"
    echo "SOURCE   : $SOURCE_TYPE"
    echo "GIT_REPO : ${GIT_REPO:-}"
    echo "REGISTRY : $APPS/$APP_SLUG.conf"
    echo
    echo "Cek: sudo deploy-laravel-node02 list"
    echo "Update nanti: sudo deploy-laravel-node02 update $APP_SLUG"
}

cmd_list(){
    require_root
    shopt -s nullglob
    local fs=("$APPS"/*.conf)
    printf "%-20s %-36s %-8s %-10s %s\n" "SLUG" "DOMAIN" "PHP" "SOURCE" "APP_DIR"
    printf '%*s\n' 110 '' | tr ' ' '-'
    if [[ ${#fs[@]} -eq 0 ]]; then
        echo "Belum ada aplikasi terdaftar."
        return
    fi
    local f
    for f in "${fs[@]}"; do
        # shellcheck disable=SC1090
        source "$f"
        printf "%-20s %-36s %-8s %-10s %s\n" \
            "$APP_SLUG" \
            "$DOMAIN" \
            "${PHP_VERSION:-$DEFAULT_PHP_VERSION}" \
            "$SOURCE_TYPE" \
            "$APP_DIR"
    done
}

cmd_info(){
    require_root
    [[ $# -ge 1 ]] || die "Usage: deploy-laravel-node02 info <slug>"
    loadconf "$1"
    echo "APP_NAME    : $APP_NAME"
    echo "APP_SLUG    : $APP_SLUG"
    echo "APP_FOLDER  : $APP_FOLDER"
    echo "DOMAIN      : $DOMAIN"
    echo "APP_DIR     : $APP_DIR"
    echo "PHP_VERSION : $PHP_VERSION"
    echo "SOURCE_TYPE : $SOURCE_TYPE"
    [[ "$SOURCE_TYPE" == git-* ]] && {
        echo "GIT_REPO    : $GIT_REPO"
        echo "GIT_BRANCH  : $GIT_BRANCH"
    }
    echo "DB_NAME     : $DB_NAME"
    echo "DB_USER     : $DB_USER"
    echo "FORCE_HTTPS : $FORCE_HTTPS"
    echo "NGINX PORT  : $APP_PORT"
    echo "PHP-FPM     : $(php_fpm_sock "$PHP_VERSION")"
    echo "Nginx       : /etc/nginx/sites-available/$APP_SLUG"
    echo "Proxy tmpl  : $PROXY_SNIPPETS/$APP_SLUG.conf"
}

cmd_help(){
    cat <<HELP
Laravel Deployment Manager - Worker Node02
Worker: ${WORKER_IP}
Nginx backend port: ${APP_PORT}

Usage:
  sudo deploy-laravel-node02 init
  sudo deploy-laravel-node02 register
  sudo deploy-laravel-node02 update <slug>
  sudo deploy-laravel-node02 update <slug> /tmp/app.zip
  sudo deploy-laravel-node02 set-php <slug> 8.2|8.3
  sudo deploy-laravel-node02 list
  sudo deploy-laravel-node02 info <slug>

Register:
  Daftarkan app yang folder-nya sudah ada (mis. init terhenti di tengah jalan).

Source init:
  1 Public GitHub HTTPS
  2 Private GitHub SSH Deploy Key (key unik per aplikasi)
  3 ZIP

PHP:
  - Saat init, pilih PHP 8.2 atau 8.3 per aplikasi
  - Site lama tetap 8.2; app baru (anjab) pilih 8.3
  - Conf menyimpan PHP_VERSION di ${APPS}/<slug>.conf

Catatan:
- Port 80 Node02 tidak disentuh (tetap untuk Docker/Geoportal).
- Semua vhost Laravel listen di port ${APP_PORT}.
- Reverse proxy 10.90.120.6 diarahkan ke http://${WORKER_IP}:${APP_PORT}.
- Registry: ${APPS}/<slug>.conf
- Proxy template: ${PROXY_SNIPPETS}/<slug>.conf
HELP
}

main(){
    local cmd="${1:-help}"
    shift || true
    case "$cmd" in
        init) cmd_init "$@" ;;
        register|attach) cmd_register "$@" ;;
        update) cmd_update "$@" ;;
        set-php) cmd_set_php "$@" ;;
        list) cmd_list "$@" ;;
        info) cmd_info "$@" ;;
        help|-h|--help) cmd_help ;;
        *) cmd_help; exit 1 ;;
    esac
}

main "$@"
