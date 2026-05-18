<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Package;
use App\Models\UserPackage;
use Illuminate\Support\Facades\DB;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $packageId = $this->data['assign_new_package'] ?? null;

        if ($packageId) {
            $package = Package::find($packageId);
            if ($package) {
                $user = $this->record;

                // 1. Actualizamos los créditos totales del usuario
                $nuevosCreditos = ($user->credits ?? 0) + $package->credits;
                $user->update(['credits' => $nuevosCreditos]);

                // 2. Calculamos la fecha de caducidad de forma inteligente
                $expiresAt = null;
                if ($package->validity_days === 0 || $package->validity_days === '0') {
                    // ♾️ SIN CADUCIDAD: Le damos 100 años de validez
                    $expiresAt = now()->addYears(100);
                } elseif (is_null($package->validity_days)) {
                    // 📅 FIN DE MES: Si está en blanco
                    $expiresAt = now()->endOfMonth();
                } else {
                    // ⏳ DÍAS NORMALES: Sumamos los días indicados
                    $expiresAt = now()->addDays((int) $package->validity_days);
                }

                // 3. Creamos el bono usando el MODELO
                UserPackage::create([
                    'gym_id' => $package->gym_id,
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'type' => $package->type,
                    'limit_type' => $package->limit_type,
                    'limit_amount' => $package->limit_amount,
                    'is_paid' => false,
                    'remaining_credits' => $package->credits,
                    'expires_at' => $expiresAt,
                ]);

                // Limpiamos el formulario para que no se duplique
                $this->data['credits'] = $nuevosCreditos;
                $this->data['assign_new_package'] = null;
            }
        }
    }
}
