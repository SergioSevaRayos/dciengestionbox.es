<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use \App\Traits\BelongsToGym;

    protected $fillable = ['gym_id', 'name', 'category'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function personalRecords()
    {
        return $this->hasMany(PersonalRecord::class);
    }
}
