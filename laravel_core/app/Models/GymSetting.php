<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymSetting extends Model
{
    use \App\Traits\BelongsToGym;

    protected $fillable = [
        'gym_id','gym_name', 'primary_color', 'logo', 'favicon', 'background_color'];

    
}