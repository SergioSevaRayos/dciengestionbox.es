<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use \App\Traits\BelongsToGym;

    protected $fillable = [
        'gym_id',
        'type',
        'limit_type',
        'limit_amount',
        'name',
        'credits',
        'price',
        'validity_days',
    ];

    protected $casts = [
        'validity_days' => 'integer',
        'limit_amount' => 'integer',
        'price' => 'decimal:2',
    ];

    public function userPackages() { return $this->hasMany(UserPackage::class); }
}
