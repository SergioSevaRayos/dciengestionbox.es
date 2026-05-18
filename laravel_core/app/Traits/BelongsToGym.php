<?php

namespace App\Traits;

use App\Models\Gym;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToGym
{
    protected static function bootBelongsToGym()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            try {
                // Si estamos en Filament y hay un gimnasio activo, filtramos por la fuerza
                if (auth()->check() && class_exists(Filament::class) && Filament::isServing()) {
                    $tenant = Filament::getTenant();
                    if ($tenant) {
                        $builder->where($builder->getModel()->getTable() . '.gym_id', $tenant->id);
                    }
                }
            } catch (\Exception $e) {
                // Silenciamos errores durante la carga inicial
            }
        });
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class, 'gym_id');
    }
}
