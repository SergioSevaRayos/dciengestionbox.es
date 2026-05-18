<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassTypeResource\Pages;
use App\Filament\Resources\ClassTypeResource\RelationManagers;
use App\Models\ClassType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassTypeResource extends Resource
{
    protected static ?string $model = ClassType::class;

    // --- CONFIGURACIÓN DEL MENÚ Y TRADUCCIONES ---
    protected static ?string $modelLabel = 'Disciplina';
    protected static ?string $pluralModelLabel = 'Disciplinas';
    protected static ?string $navigationLabel = 'Disciplinas (Tipos)';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Gestión Deportiva'; 
    protected static ?int $navigationSort = 2; 
    // ---------------------------------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre de la disciplina')
                    ->placeholder('Ej: CrossFit, Yoga, Pilates...')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\ColorPicker::make('color')
                    ->label('Color en el calendario')
                    ->default('#000000'),
                
                Forms\Components\Textarea::make('description')
                    ->label('Descripción de la clase')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Disciplina')
                    ->searchable(),
                
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassTypes::route('/'),
            'create' => Pages\CreateClassType::route('/create'),
            'edit' => Pages\EditClassType::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
    }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
    }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
    }


    


    public static function getNavigationLabel(): string
    {
        $label = parent::getNavigationLabel();
        $isSubscribed = \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
        if ($isSubscribed) {
            return $label;
        }
        return $isSubscribed ? $label : $label . " 🔒";
    }

}