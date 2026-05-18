<?php
namespace App\Filament\Resources\GymSessionResource\Pages;

use App\Filament\Resources\GymSessionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\GymSession;
use Carbon\Carbon;

class CreateGymSession extends CreateRecord
{
    protected static string $resource = GymSessionResource::class;

    private array $beforeSaveData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->beforeSaveData = [
            'is_recurring' => $data['is_recurring'] ?? false,
            'days_of_week' => array_map('intval', $data['days_of_week'] ?? []),
            'repeat_until' => $data['repeat_until'] ?? null,
        ];
        unset($data['is_recurring'], $data['days_of_week'], $data['repeat_until']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $mainRecord = $this->record;
        $info = $this->beforeSaveData;

        if (!$info['is_recurring'] || empty($info['days_of_week']) || !$info['repeat_until']) {
            return;
        }

        $startDate = Carbon::parse($mainRecord->start_time);
        $limitDate = Carbon::parse($info['repeat_until'])->endOfDay();
        $duration = $startDate->diffInMinutes($mainRecord->end_time);
        $currentDate = $startDate->copy()->addDay();

        while ($currentDate->lte($limitDate)) {
            if (in_array($currentDate->dayOfWeek, $info['days_of_week'])) {
                GymSession::create([
                    'class_type_id' => $mainRecord->class_type_id,
                    'capacity'      => $mainRecord->capacity,
                    'start_time'    => $currentDate->copy()->setTimeFrom($startDate),
                    'end_time'      => $currentDate->copy()->setTimeFrom($startDate)->addMinutes($duration),
                ]);
            }
            $currentDate->addDay();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
