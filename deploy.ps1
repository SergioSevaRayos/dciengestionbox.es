# ============================================================
# DCIEN GestiónBox — Script de Deploy
# ============================================================
# Uso: .\deploy.ps1 [-build] [-frontend] [-backend] [-views] [-all]
# ============================================================

param(
    [switch]$build,
    [switch]$frontend,
    [switch]$backend,
    [switch]$views,
    [switch]$all
)

# ─── CONFIGURACIÓN DEL SERVIDOR ─────────────────────────────
$SSH_HOST   = "147.79.103.7"
$SSH_PORT   = "65002"
$SSH_USER   = "u755459505"
$SSH_PASS   = "9400Jet_"
$DOMAIN     = "dciengestionbox.es"
$SERVER_ROOT = "/home/$SSH_USER/domains/$DOMAIN"

# Rutas remotas
$REMOTE_LARAVEL  = "$SERVER_ROOT/laravel_core"
$REMOTE_PUBLIC   = "$SERVER_ROOT/public_html"

# Rutas locales (relativas al script)
$LOCAL_ROOT      = $PSScriptRoot
$LOCAL_LARAVEL   = "$LOCAL_ROOT\laravel_core"
$LOCAL_PUBLIC    = "$LOCAL_ROOT\public_html"
# ─────────────────────────────────────────────────────────────

# Helper para ejecutar SCP con contraseña (usa sshpass si está disponible,
# si no lo está muestra instrucciones)
function Upload {
    param([string]$source, [string]$dest, [string]$label)
    Write-Host ""
    Write-Host "  ↑ Subiendo $label..." -ForegroundColor Cyan

    # Comprueba si sshpass está disponible (WSL / Git Bash)
    $hasSshpass = Get-Command "sshpass" -ErrorAction SilentlyContinue
    if ($hasSshpass) {
        sshpass -p $SSH_PASS scp -r -P $SSH_PORT -o StrictHostKeyChecking=no $source "${SSH_USER}@${SSH_HOST}:${dest}"
    } else {
        # Sin sshpass: SCP estándar (pedirá contraseña manualmente)
        Write-Host "  ⚠  sshpass no encontrado. Se pedirá la contraseña manualmente." -ForegroundColor Yellow
        Write-Host "  ⚠  Contraseña: $SSH_PASS" -ForegroundColor Yellow
        scp -r -P $SSH_PORT -o StrictHostKeyChecking=no $source "${SSH_USER}@${SSH_HOST}:${dest}"
    }

    if ($LASTEXITCODE -eq 0) {
        Write-Host "  ✓ $label subido correctamente." -ForegroundColor Green
    } else {
        Write-Host "  ✗ Error subiendo $label." -ForegroundColor Red
        exit 1
    }
}

# Helper para ejecutar comandos remotos vía SSH
function RemoteCmd {
    param([string]$cmd, [string]$label)
    Write-Host ""
    Write-Host "  ⚙  $label" -ForegroundColor Magenta

    $hasSshpass = Get-Command "sshpass" -ErrorAction SilentlyContinue
    if ($hasSshpass) {
        sshpass -p $SSH_PASS ssh -p $SSH_PORT -o StrictHostKeyChecking=no "${SSH_USER}@${SSH_HOST}" $cmd
    } else {
        ssh -p $SSH_PORT -o StrictHostKeyChecking=no "${SSH_USER}@${SSH_HOST}" $cmd
    }
}

# ─── ACCIÓN: BUILD VITE ─────────────────────────────────────
function DoBuild {
    Write-Host ""
    Write-Host "━━━ BUILD VITE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkCyan
    Set-Location "$LOCAL_LARAVEL"
    Write-Host "  → npm run build" -ForegroundColor Gray
    npm run build
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  ✗ Build fallido. Abortando." -ForegroundColor Red
        exit 1
    }
    Write-Host "  ✓ Build completado." -ForegroundColor Green
    Set-Location $LOCAL_ROOT
}

# ─── ACCIÓN: SUBIR ASSETS COMPILADOS (public/build) ─────────
function DoFrontend {
    Write-Host ""
    Write-Host "━━━ FRONTEND (assets Vite compilados) ━━━━━━━━" -ForegroundColor DarkCyan
    Upload "$LOCAL_LARAVEL\public\build" "$REMOTE_LARAVEL/public/build" "laravel_core/public/build"
    # CSS plano (no-Vite, legacy)
    if (Test-Path "$LOCAL_LARAVEL\public\css") {
        Upload "$LOCAL_LARAVEL\public\css" "$REMOTE_LARAVEL/public/css" "laravel_core/public/css"
    }
}

# ─── ACCIÓN: SUBIR public_html ───────────────────────────────
function DoPublicHtml {
    Write-Host ""
    Write-Host "━━━ PUBLIC HTML (entrada web) ━━━━━━━━━━━━━━━━" -ForegroundColor DarkCyan
    # Subimos todo excepto storage/ (es symlink en el servidor)
    # Copiamos los archivos directamente listados
    $items = @(
        "index.php",
        "sw.js",
        "robots.txt",
        "sitemap.xml",
        "favicon.ico",
        "favicon.png",
        "logo-app.png",
        "logo-training-nav.png",
        "logo-training.png",
        "logo-light.png",
        "logo.png",
        "icon-192.png",
        "icon-512.png",
        "manifest-admin.json",
        "manifest-user.json",
        "manifest.json"
    )
    foreach ($item in $items) {
        $path = "$LOCAL_PUBLIC\$item"
        if (Test-Path $path) {
            Upload $path "$REMOTE_PUBLIC/$item" "public_html/$item"
        }
    }
    # Carpeta images si existe
    if (Test-Path "$LOCAL_PUBLIC\images") {
        Upload "$LOCAL_PUBLIC\images" "$REMOTE_PUBLIC/images" "public_html/images"
    }
}

# ─── ACCIÓN: SUBIR CÓDIGO PHP (backend Laravel) ─────────────
function DoBackend {
    Write-Host ""
    Write-Host "━━━ BACKEND (código PHP Laravel) ━━━━━━━━━━━━━" -ForegroundColor DarkCyan

    # Directorios principales de la app
    $dirs = @("app", "config", "database", "routes", "bootstrap")
    foreach ($dir in $dirs) {
        if (Test-Path "$LOCAL_LARAVEL\$dir") {
            Upload "$LOCAL_LARAVEL\$dir" "$REMOTE_LARAVEL/$dir" "laravel_core/$dir"
        }
    }

    # artisan (archivo suelto)
    if (Test-Path "$LOCAL_LARAVEL\artisan") {
        Upload "$LOCAL_LARAVEL\artisan" "$REMOTE_LARAVEL/artisan" "laravel_core/artisan"
    }

    # Limpiar caché en el servidor después de subir PHP
    RemoteCmd "cd $REMOTE_LARAVEL && php artisan config:clear && php artisan route:clear && php artisan view:clear" "Limpiando caché de Laravel en el servidor..."
}

# ─── ACCIÓN: SUBIR SOLO VISTAS BLADE ────────────────────────
function DoViews {
    Write-Host ""
    Write-Host "━━━ VIEWS (Blade templates) ━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkCyan
    Upload "$LOCAL_LARAVEL\resources\views" "$REMOTE_LARAVEL/resources/views" "laravel_core/resources/views"
    RemoteCmd "cd $REMOTE_LARAVEL && php artisan view:clear" "Limpiando caché de vistas..."
}

# ─── LÓGICA PRINCIPAL ────────────────────────────────────────

Write-Host ""
Write-Host "╔══════════════════════════════════════════════╗" -ForegroundColor DarkYellow
Write-Host "║     DCIEN GestiónBox — Deploy              ║" -ForegroundColor DarkYellow
Write-Host "║     $DOMAIN                  ║" -ForegroundColor DarkYellow
Write-Host "╚══════════════════════════════════════════════╝" -ForegroundColor DarkYellow
Write-Host ""

if (-not ($build -or $frontend -or $backend -or $views -or $all)) {
    Write-Host "Uso:" -ForegroundColor White
    Write-Host "  .\deploy.ps1 -build           Solo compilar Vite (sin subir)" -ForegroundColor Gray
    Write-Host "  .\deploy.ps1 -build -frontend  Build + subir assets compilados" -ForegroundColor Gray
    Write-Host "  .\deploy.ps1 -backend          Subir código PHP (app/, config/, routes/...)" -ForegroundColor Gray
    Write-Host "  .\deploy.ps1 -views            Subir solo vistas Blade" -ForegroundColor Gray
    Write-Host "  .\deploy.ps1 -all              Build + subir todo (frontend + backend + public_html)" -ForegroundColor Gray
    Write-Host ""
    exit 0
}

if ($all) {
    DoBuild
    DoFrontend
    DoPublicHtml
    DoBackend
    DoViews
} else {
    if ($build)    { DoBuild }
    if ($frontend) { DoFrontend }
    if ($backend)  { DoBackend }
    if ($views)    { DoViews }
}

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkGreen
Write-Host "  ✓ Deploy completado — Verifica en https://$DOMAIN" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkGreen
Write-Host ""
