<?php
require __DIR__.'/../laravel_core/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

try {
    Mail::raw('Este es un test desde el NAVEGADOR (Web) de DCIEN', function ($msg) {
        $msg->to('ssrspaingroup@gmail.com')->subject('TEST WEB DCIEN');
    });
    echo "<h1>¡ÉXITO! El servidor web ha enviado el correo.</h1>";
} catch (\Exception $e) {
    echo "<h1>ERROR:</h1><pre>" . $e->getMessage() . "</pre>";
}
