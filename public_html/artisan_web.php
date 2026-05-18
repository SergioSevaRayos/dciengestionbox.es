<?php
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../laravel_core/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

function run($command, $kernel) {
    echo "\n> Ejecutando: php artisan $command\n";
    $input = new Symfony\Component\Console\Input\StringInput($command);
    $output = new Symfony\Component\Console\Output\BufferedOutput();
    $kernel->handle($input, $output);
    echo $output->fetch();
}

try {
    // 1. Limpiamos todo
    run('optimize:clear', $kernel);
    
    // 2. Publicamos los archivos de Livewire en public_html
    // Esto es vital para que el navegador encuentre los scripts y no dé 404
    run('livewire:publish --assets --force', $kernel);
    
    echo "\n✅ Proceso finalizado.";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
}
