<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalRecord extends Model
{
    use \App\Traits\BelongsToGym;

    protected $fillable = [
        'gym_id','user_id', 'exercise_id', 'weight', 'achieved_at'];

    protected function casts(): array
    {
        return [
            'achieved_at' => 'date',
            'weight' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    
}