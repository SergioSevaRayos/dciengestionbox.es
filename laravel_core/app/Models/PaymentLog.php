<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    protected $fillable = [
        'gym_id', 'bono_name', 'client_name', 'activated_at', 
        'activated_by', 'paid_at', 'paid_by', 'observations',
        'status', 'cancellation_reason'
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
