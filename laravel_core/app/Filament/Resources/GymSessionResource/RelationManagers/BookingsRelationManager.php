<?php
namespace App\Filament\Resources\GymSessionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Booking;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotifyAction;
use Illuminate\Database\Eloquent\Model;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';
    protected static ?string $title = 'Alumnos Apuntados';

    public function form(Form $form): Form {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Alumno')
                ->required()
                ->searchable(),
            Forms\Components\Select::make('status')
                ->label('Estado')
                ->options(['booked' => 'Apuntado', 'waiting' => 'En Espera', 'cancelled' => 'Cancelado'])
                ->default('booked')
                ->required(),
        ]);
    }

    public function table(Table $table): Table {
        return $table
            ->poll('3s') 
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Alumno'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) { 'booked' => 'success', 'waiting' => 'warning', 'cancelled' => 'danger', default => 'gray' })
                    ->formatStateUsing(fn (string $state): string => match ($state) { 'booked' => 'Apuntado', 'waiting' => 'En Espera', 'cancelled' => 'Cancelado', default => $state }),
            ])
            ->headerActions([ 
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['gym_id'] = $this->getOwnerRecord()->gym_id;
                        return $data;
                    })
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->label('Desapuntar')
                    ->action(function (Booking $record, RelationManager $livewire) {
                        $gymSession = $livewire->getOwnerRecord();
                        
                        // Solo pedimos el borrado al modelo
                        $borradoOk = $record->delete();
                        
                        if ($borradoOk) {
                            $siguiente = $gymSession->bookings()->where('status', 'waiting')->orderBy('created_at', 'asc')->first();
                            if ($siguiente) {
                                Notification::make()->title('¡Hueco libre en tu clase!')->body('El administrador ha liberado una plaza. ¿La quieres?')
                                    ->actions([ NotifyAction::make('aceptar')->label('Aceptar Plaza')->button()->color('success')->url("/confirmar-reserva/{$siguiente->id}") ])
                                    ->sendToDatabase($siguiente->user);
                            }
                        }
                    }),
            ]);
    }
}
