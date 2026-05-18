<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        // Solo podemos usar los nombres que existen en App\Enums\ExerciseIcon
        $exercises = [
            ['name' => 'Back Squat', 'category' => 'Powerlifting'],
            ['name' => 'Bench Press', 'category' => 'Powerlifting'],
            ['name' => 'Deadlift', 'category' => 'Powerlifting'],
            ['name' => 'Front Squat', 'category' => 'Halterofilia'],
            ['name' => 'Snatch', 'category' => 'Halterofilia'],
            ['name' => 'Clean & Jerk', 'category' => 'Halterofilia'],
            ['name' => 'Push Press', 'category' => 'CrossFit'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::updateOrCreate(
                ['name' => $exercise['name']],
                ['category' => $exercise['category']]
            );
        }
    }
}
