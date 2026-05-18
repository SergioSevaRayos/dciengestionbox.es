<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $randomCode = Str::random(5);
        
        return [
            'name' => 'Usuario Prueba ' . $randomCode,
            'email' => strtolower($randomCode) . '@ejemplo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // contraseña: password
            'remember_token' => Str::random(10),
            'role' => 'admin', // Te hacemos administrador
            'credits' => 50,   // Te damos 50 créditos para probar las reservas
        ];
    }
}
