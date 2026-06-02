# DCIEN GestiónBox — Guía de Deploy

## Estructura del proyecto

```
dciengestionbox.es/
├── laravel_core/           ← Código Laravel (PHP, vistas, assets fuente)
│   ├── app/                ← Modelos, Filament resources, providers...
│   ├── config/             ← Configuración de Laravel
│   ├── database/           ← Migraciones y seeders
│   ├── resources/
│   │   ├── views/          ← Blade templates
│   │   ├── js/             ← Fuentes JS (Vite)
│   │   └── css/            ← Fuentes CSS (Vite)
│   ├── routes/             ← web.php, console.php
│   └── public/
│       └── build/          ← Assets compilados por Vite (NO editar)
└── public_html/            ← Entrada web (index.php, logos, sw.js...)
```

---

## Qué editar según el cambio

| Quiero cambiar... | Edito en... | Build? | Flag |
|---|---|---|---|
| Modelos, recursos Filament, providers | `laravel_core/app/` | ❌ No | `-backend` |
| Rutas | `laravel_core/routes/` | ❌ No | `-backend` |
| Configuración Laravel | `laravel_core/config/` | ❌ No | `-backend` |
| Migraciones / seeders | `laravel_core/database/` | ❌ No | `-backend` |
| Vistas Blade | `laravel_core/resources/views/` | ❌ No | `-views` |
| JS / CSS (assets Vite) | `laravel_core/resources/js/` o `css/` | ✅ Sí | `-build -frontend` |
| `index.php`, logos, `sw.js`, manifests | `public_html/` | ❌ No | `-all` (incluido) |
| Todo a la vez | todo | ✅ Sí | `-all` |

---

## Comandos de deploy

Abre PowerShell en `C:\Users\Trending Pc\Documents\dciengestionbox.es`

### Caso 1 — Solo cambié código PHP (modelos, Filament, rutas, config...)

```powershell
.\deploy.ps1 -backend
```

### Caso 2 — Solo cambié vistas Blade

```powershell
.\deploy.ps1 -views
```

### Caso 3 — Solo cambié JS/CSS (assets Vite)

```powershell
.\deploy.ps1 -build -frontend
```

### Caso 4 — Cambié varias cosas (PHP + vistas)

```powershell
.\deploy.ps1 -backend -views
```

### Caso 5 — Deploy completo (todo)

```powershell
.\deploy.ps1 -all
```

Hace: build Vite → sube assets compilados → sube public_html → sube código PHP → sube vistas → limpia caché Laravel.

### Caso 6 — Solo hacer build sin subir

```powershell
.\deploy.ps1 -build
```

---

## Primer uso — Habilitar scripts en PowerShell

Ejecuta esto una sola vez:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

---

## Conexión al servidor

| Dato | Valor |
|---|---|
| Host | `147.79.103.7` |
| Puerto SSH | `65002` |
| Usuario | `u755459505` |
| Contraseña | `9400Jet_` |
| Dominio | `dciengestionbox.es` |

Para conectarte por SSH directamente:

```powershell
ssh -p 65002 u755459505@147.79.103.7
```

---

## Bajar cambios del servidor al local

Si hiciste cambios directamente en el servidor vía FTP/SSH y quieres traerlos al local:

```powershell
# Código PHP
scp -P 65002 -r u755459505@147.79.103.7:/home/u755459505/domains/dciengestionbox.es/laravel_core/app "C:\Users\Trending Pc\Documents\dciengestionbox.es\laravel_core\"

# Vistas Blade
scp -P 65002 -r u755459505@147.79.103.7:/home/u755459505/domains/dciengestionbox.es/laravel_core/resources/views "C:\Users\Trending Pc\Documents\dciengestionbox.es\laravel_core\resources\"

# public_html
scp -P 65002 -r u755459505@147.79.103.7:/home/u755459505/domains/dciengestionbox.es/public_html "C:\Users\Trending Pc\Documents\dciengestionbox.es\"
```

---

## Notas importantes

- **Nunca subas `vendor/`** — está instalado en el servidor con `composer install`.
- **Nunca subas `node_modules/`** — no se necesita en el servidor.
- **Nunca subas `.env`** — el `.env` de producción está en el servidor, no lo sobreescribas.
- **Nunca subas `storage/`** — contiene logs y archivos subidos por los usuarios en producción.
- **Después de subir**, verifica siempre en `https://dciengestionbox.es` que todo funciona.
- El script limpia automáticamente la caché de Laravel (`config:clear`, `route:clear`, `view:clear`) después de cada subida de backend o vistas.
- Si el servidor tiene cambios que no tienes en local, **baja primero** antes de subir.
