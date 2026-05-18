<?php

namespace App\Filament\System\Resources;

use App\Filament\System\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administración Global';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\Select::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin de Gym',
                        'student' => 'Alumno',
                    ])->required(),
                // 🚀 CORRECCIÓN: Usamos la relación 'gyms' (BelongsToMany) y permitimos selección múltiple
                Forms\Components\Select::make('gyms')
                    ->relationship('gyms', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Gimnasios Asociados'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('welcome_email_sent')
                    ->label('Aviso Email')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                // 🚀 CORRECCIÓN: Llamamos a gyms.name. Al ser BelongsToMany, Filament crea las etiquetas solo.
                // Quitamos el ->sortable() porque no se puede ordenar por una columna múltiple fácilmente en SQL.
                Tables\Columns\TextColumn::make('gyms.name')
                    ->label('Gimnasio(s)')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'student' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 🚀 CORRECCIÓN: El filtro ahora usa la relación 'gyms' para buscar en la tabla pivote
                SelectFilter::make('gyms')
                    ->relationship('gyms', 'name')
                    ->label('Filtrar por Gimnasio')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('role')
                    ->label('Filtrar por Rol')
                    ->options([
                        'super_admin' => 'Super Admins',
                        'admin' => 'Admins de Gym',
                        'student' => 'Alumnos',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
