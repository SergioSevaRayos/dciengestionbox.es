<?php

namespace App\Filament\System\Resources\UserResource\Pages;

use App\Filament\System\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (empty($data['password'])) {
            $data['password'] = bcrypt(Str::random(24));
        }

        $newUser = static::getModel()::create($data);

        try {
            $token = Password::createToken($newUser);
            $newUser->notify(new \App\Notifications\WelcomeSetPasswordNotification($token));
            
            // Forzamos el guardado directo saltándonos el $fillable
            $newUser->welcome_email_sent = true;
            $newUser->save();
        } catch (\Exception $e) {
            Log::error("Error al enviar email desde System Panel a {$newUser->email}: " . $e->getMessage());
        }

        return $newUser;
    }
}
