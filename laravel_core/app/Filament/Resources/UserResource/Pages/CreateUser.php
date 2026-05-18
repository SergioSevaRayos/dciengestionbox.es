<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $gym = Filament::getTenant();
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            $existingUser->gyms()->syncWithoutDetaching([$gym->id]);
            
            if (!empty($data['credits'])) {
                $existingUser->increment('credits', $data['credits']);
            }
            
            return $existingUser;
        }

        $data['password'] = bcrypt(Str::random(24));
        $newUser = static::getModel()::create($data);
        $newUser->gyms()->attach($gym->id);

        try {
            $token = Password::createToken($newUser);
            $newUser->notify(new \App\Notifications\WelcomeSetPasswordNotification($token));
            
            // Forzamos el guardado directo saltándonos el $fillable
            $newUser->welcome_email_sent = true;
            $newUser->save();
        } catch (\Exception $e) {
            Log::error("Error al enviar email de bienvenida a {$newUser->email}: " . $e->getMessage());
        }

        return $newUser;
    }
}
