<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassType extends Model
{
    use \App\Traits\BelongsToGym;

    // Le damos permiso a Laravel para guardar estos campos desde el formulario
    protected $fillable = [
        'gym_id',
        'name',
        'color',
        'description',
    ];

    
}