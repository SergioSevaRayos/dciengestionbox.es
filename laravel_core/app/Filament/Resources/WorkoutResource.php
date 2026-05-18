<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkoutResource\Pages;
use App\Models\Workout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class WorkoutResource extends Resource
{
    protected static ?string $model = Workout::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Entrenamiento';
    protected static ?string $modelLabel = 'WOD';
    protected static ?string $pluralModelLabel = 'WODs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Definición del WOD')->schema([
                    // ✨ CAMBIO: Sustituimos Gym ID por Fecha
                    Forms\Components\Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nombre (Ej: WOD 16/04 o Murph)')
                            ->required(),
                        DatePicker::make('date') // ✨ ¡ESTO ES LA CLAVE! El gestor planifica por día.
                            ->label('Fecha')
                            ->default(now())
                            ->required(),
                    ]),
                    // ✨ MEJORA: Usamos editor enriquecido con los mismos botones que Benchmarks (imagen 1.png)
                    RichEditor::make('content')
                        ->label('Desarrollo del Entrenamiento')
                        ->required()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3', 'bulletList', 'orderedList', 
                            'redo', 'undo', 'link'
                        ])
                        ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ✨ CAMBIO: Sustituimos Gym ID por columna de Fecha formateada
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y') // Formato legible DD/MM/AAAA
                    ->sortable(),
                TextColumn::make('name')
                    ->label('WOD')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('font-black')
                    ->color('primary'),
                // ✨ MEJORA: Mostramos la descripción usando la lista de chevrons que creamos en el Paso 1
                TextColumn::make('content')
                    ->label('Descripción')
                    ->view('filament.admin.components.wod-list-description') // Cargamos el componente visual
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkouts::route('/'),
            'create' => Pages\CreateWorkout::route('/create'),
            'edit' => Pages\EditWorkout::route('/{record}/edit'),
        ];
    }
}
