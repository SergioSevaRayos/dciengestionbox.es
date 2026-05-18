<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;

enum ExerciseIcon: string implements HasIcon
{
    // === SQUATS (Sentadillas) ===
    case BackSquat = 'Sentadilla Trasera (Back Squat)';
    case FrontSquat = 'Sentadilla Frontal (Front Squat)';
    case OverheadSquat = 'Sentadilla sobre la cabeza (Overhead Squat)';
    
    // === DEADLIFTS (Pesos Muertos) ===
    case Deadlift = 'Peso Muerto (Deadlift)';
    case RDL = 'Peso Muerto Rumano (RDL)';
    case SumoDeadlift = 'Peso Muerto Sumo';
    
    // === PRESSES (Empujes) ===
    case BenchPress = 'Press de Banca (Bench Press)';
    case StrictPress = 'Press Militar Estricto (Strict Press)';
    case PushPress = 'Push Press';
    case PushJerk = 'Push Jerk';
    case SplitJerk = 'Split Jerk';
    
    // === OLYMPIC WEIGHTLIFTING (Halterofilia) ===
    case PowerClean = 'Cargada de Potencia (Power Clean)';
    case SquatClean = 'Cargada Completa (Squat Clean)';
    case PowerSnatch = 'Arrancada de Potencia (Power Snatch)';
    case SquatSnatch = 'Arrancada Completa (Squat Snatch)';
    case CleanAndJerk = 'Dos Tiempos (Clean & Jerk)';
    case Thruster = 'Thruster';
    case Cluster = 'Cluster';
    
    // === GYMNASTICS / BODYWEIGHT CON LASTRE ===
    case WeightedPullups = 'Dominadas con Lastre (Weighted Pull-ups)';
    case WeightedDips = 'Fondos con Lastre (Weighted Dips)';
    
    // === ACCESORIOS Y HYROX ===
    case BarbellRow = 'Remo con Barra (Barbell Row)';
    case SledPush = 'Empuje de Trineo (Sled Push)';
    case SledPull = 'Tirón de Trineo (Sled Pull)';
    case FarmersCarry = 'Paseo del Granjero (Farmers Carry)';
    case SandbagLunges = 'Zancadas con Saco (Sandbag Lunges)';

    // Función que devuelve el icono correspondiente según el tipo de movimiento
    public function getIcon(): ?string
    {
        return match ($this) {
            self::BackSquat, self::FrontSquat, self::OverheadSquat, self::SandbagLunges => 'heroicon-m-arrows-up-down',
            self::Deadlift, self::RDL, self::SumoDeadlift => 'heroicon-m-minus-small',
            self::BenchPress => 'heroicon-m-archive-box',
            self::StrictPress, self::PushPress, self::PushJerk, self::SplitJerk => 'heroicon-m-arrow-up-tray',
            self::PowerClean, self::SquatClean => 'heroicon-m-sparkles',
            self::PowerSnatch, self::SquatSnatch, self::CleanAndJerk => 'heroicon-m-bolt',
            self::Thruster, self::Cluster => 'heroicon-m-rocket-launch',
            self::WeightedPullups, self::WeightedDips => 'heroicon-m-arrow-up',
            self::BarbellRow => 'heroicon-m-bars-3',
            self::SledPush, self::SledPull => 'heroicon-m-arrows-right-left',
            self::FarmersCarry => 'heroicon-m-briefcase',
        };
    }
}
