<?php
header('Content-Type: text/plain');

// Intentamos cargar el autoload desde laravel_core
require __DIR__.'/../laravel_core/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::capture());

use App\Models\Exercise;
use App\Models\Gym;

echo "--- DIAGNÓSTICO DE BASE DE DATOS ---\n\n";

try {
    $count = Exercise::count();
    echo "Total de ejercicios en la BD: " . $count . "\n";

    if ($count > 0) {
        echo "\nÚltimos 10 ejercicios registrados:\n";
        $exercises = Exercise::latest()->limit(10)->get();
        foreach ($exercises as $ex) {
            echo "ID: {$ex->id} | Nombre: {$ex->name} | Gym_ID: " . ($ex->gym_id ?? 'NULL') . " | Cat: {$ex->category}\n";
        }
    } else {
        echo "⚠️ LA TABLA 'exercises' ESTÁ VACÍA.\n";
    }

    echo "\n--- GIMNASIOS (TENANTS) ---\n";
    $gyms = Gym::all();
    foreach ($gyms as $g) {
        echo "ID: {$g->id} | Nombre: {$g->name} | Slug: {$g->slug}\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
}
