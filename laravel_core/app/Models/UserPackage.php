<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserPackage extends Model
{
    protected $fillable = ['gym_id', 'user_id', 'package_id', 'type', 'limit_type', 'limit_amount', 'is_paid', 'remaining_credits', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime', 'is_paid' => 'boolean', 'limit_amount' => 'integer'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
    public function gym(): BelongsTo { return $this->belongsTo(Gym::class); }

    protected static function booted()
    {
        static::creating(function ($userPackage) {
            if (!$userPackage->gym_id) {
                $user = \App\Models\User::find($userPackage->user_id);
                $package = \App\Models\Package::find($userPackage->package_id);
                $userPackage->gym_id = $user?->gym_id ?? $package?->gym_id ?? $user?->gyms()->first()?->id ?? \Filament\Facades\Filament::getTenant()?->id;
            }
            if ($userPackage->package_id && !$userPackage->type) {
                $padre = \App\Models\Package::find($userPackage->package_id);
                if ($padre) {
                    $userPackage->type = $padre->type;
                    $userPackage->limit_type = $padre->limit_type;
                    $userPackage->limit_amount = $padre->limit_amount;
                    if ($padre->type === 'bono') $userPackage->remaining_credits = $padre->credits;
                }
            }
        });

        static::created(function ($userPackage) {
            try {
                $package = \App\Models\Package::find($userPackage->package_id);
                $user = \App\Models\User::find($userPackage->user_id);
                if ($package && $user && $userPackage->gym_id) {
                    \App\Models\PaymentLog::create(['gym_id' => $userPackage->gym_id, 'bono_name' => $package->name, 'client_name' => $user->name, 'activated_at' => now(), 'activated_by' => Auth::user()?->name ?? 'Sistema', 'status' => 'activo']);
                }
            } catch (\Exception $e) { Log::error("Error silencioso en PaymentLog: " . $e->getMessage()); }
        });

        static::deleted(function ($userPackage) {
            try {
                $user = \App\Models\User::find($userPackage->user_id);
                $package = \App\Models\Package::find($userPackage->package_id);
                if ($user && $package && $userPackage->gym_id) {
                    \App\Models\PaymentLog::where('gym_id', $userPackage->gym_id)->where('client_name', $user->name)->where('bono_name', $package->name)->whereNull('paid_at')->where('status', 'activo')->latest()->first()?->update(['status' => 'cancelado']);
                }
            } catch (\Exception $e) { Log::error("Error silencioso al borrar PaymentLog: " . $e->getMessage()); }
        });
    }
}
