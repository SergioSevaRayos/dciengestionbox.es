<?php

use Illuminate\Support\Facades\Route;

// Redirigir la entrada principal de la web al login unificado
Route::get('/', function () {
    return view('welcome');
});

// Puente para el email de recuperación de contraseña (de Laravel a Filament)

Route::get('/reset-password/{token}', function (string $token) {
    return redirect()->route('filament.app.auth.password-reset.reset', [
        'token' => $token,
        'email' => request()->query('email'),
    ]);
})->name('password.reset');

// WEBHOOK DE STRIPE (SaaS)
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// --- PÁGINAS LEGALES PÚBLICAS ---
Route::view('/aviso-legal', 'legal.terms')->name('legal.terms');
Route::view('/privacidad', 'legal.privacy')->name('legal.privacy');
Route::view('/cookies', 'legal.cookies')->name('legal.cookies');

// --- RUTAS LEGALES ---
Route::view('/aviso-legal', 'legal.terms')->name('legal.terms');
Route::view('/privacidad', 'legal.privacy')->name('legal.privacy');
Route::view('/cookies', 'legal.cookies')->name('legal.cookies');

// --- PÁGINA DE CONTACTO ---
Route::view('/contacto', 'contact')->name('contact');

// Procesar el formulario de contacto
Route::post('/contacto', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

Route::get('/manifest/{gym:slug}.json', function (\App\Models\Gym $gym) {
    $logo = $gym->logo ? asset('storage/' . $gym->logo) : '/logo-app.png';
    return response()->json([
        'name' => $gym->name,
        'short_name' => $gym->name,
        'start_url' => '/app/' . $gym->slug,
        'display' => 'standalone',
        'background_color' => '#111827',
        'theme_color' => '#111827',
        'icons' => [
            [
                'src' => $logo,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ],
            [
                'src' => $logo,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ]
        ]
    ]);
});
Route::get('/login', fn() => redirect('/admin/login'))->name('login');

\Opcodes\LogViewer\Facades\LogViewer::auth(function ($request) {
    return $request->user() && $request->user()->role === 'super_admin';
});
