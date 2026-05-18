<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\GymSetting;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class ManageBranding extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $title = 'Marca y Diseño';
    protected static string $view = 'filament.pages.manage-branding';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        
        // Magia SaaS: Buscamos la config de este gimnasio. Si no existe, la crea en blanco.
        $settings = GymSetting::firstOrCreate(
            ['gym_id' => $tenant->id],
            ['gym_name' => $tenant->name, 'primary_color' => '#10b981'] // Valores por defecto
        );
        
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identidad Corporativa')
                    ->description('Personaliza cómo ven la aplicación tus usuarios. Filament adaptará tu color al modo oscuro automáticamente.')
                    ->schema([
                        TextInput::make('gym_name')
                            ->label('Nombre del Gimnasio')
                            ->required(),
                        
                        ColorPicker::make('primary_color')
                            ->label('Color Principal de Marca')
                            ->required(),

                        FileUpload::make('logo')
                            ->label('Logotipo')
                            ->image()
                            ->disk('public')
                            ->directory('branding'),

                        FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('branding'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Cambios')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $tenant = Filament::getTenant();
        
        // Actualizamos estrictamente la configuración del gimnasio activo
        $settings = GymSetting::where('gym_id', $tenant->id)->first();
        
        if ($settings) {
            $settings->update($this->form->getState());
        }

        Notification::make()
            ->title('Diseño actualizado')
            ->success()
            ->send();
    }
}
