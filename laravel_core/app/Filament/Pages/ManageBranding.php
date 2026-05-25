<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\GymSetting;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class ManageBranding extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $title = 'Marca y Diseño';
    protected static string $view = 'filament.pages.manage-branding';

    public ?string $gym_name = null;
    public ?string $primary_color = null;
    public array $logo = [];
    public array $favicon = [];
    public bool $enable_arena = false;

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        $settings = GymSetting::firstOrCreate(
            ['gym_id' => $tenant->id],
            ['gym_name' => $tenant->name, 'primary_color' => '#10b981']
        );

        $this->gym_name      = $settings->gym_name;
        $this->primary_color = $settings->primary_color;
        $this->enable_arena  = (bool) $settings->enable_arena;
        // FileUpload espera array ['ruta' => 'ruta']
        $this->logo    = $settings->logo ? [$settings->logo => $settings->logo] : [];
        $this->favicon = $settings->favicon ? [$settings->favicon => $settings->favicon] : [];

        // Sincronizar con el form de Filament
        $this->form->fill([
            'gym_name'     => $this->gym_name,
            'primary_color' => $this->primary_color,
            'enable_arena'  => $this->enable_arena,
            'logo'         => $this->logo,
            'favicon'      => $this->favicon,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identidad Corporativa')
                    ->schema([
                        TextInput::make('gym_name')
                            ->label('Nombre del Gimnasio')->required(),
                        ColorPicker::make('primary_color')
                            ->label('Color Principal')->required(),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('branding')
                            ->preserveFilenames()
                            ->image(),
                        FileUpload::make('favicon')
                            ->label('Favicon / Icono')
                            ->disk('public')
                            ->directory('branding')
                            ->avatar()
                            ->image(),
                    ])->columns(2),

                Section::make('Módulos Avanzados')
                    ->schema([
                        Toggle::make('enable_arena')
                            ->label('Módulo Competiciones')
                            ->helperText('Activa el sistema de competiciones para los atletas.')
                            ->onColor('primary'),
                    ]),
            ]);
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
        $tenant   = Filament::getTenant();
        $settings = GymSetting::where('gym_id', $tenant->id)->first();

        if ($settings) {
            // Si FileUpload devuelve array, extraer la ruta; si es string, usarlo directamente
            $logo    = is_array($this->logo)
                ? collect($this->logo)->first()
                : $this->logo;
            $favicon = is_array($this->favicon)
                ? collect($this->favicon)->first()
                : $this->favicon;

            $settings->update([
                'gym_name'      => $this->gym_name,
                'primary_color' => $this->primary_color,
                'logo'          => $logo ?: $settings->logo,
                'favicon'       => $favicon ?: $settings->favicon,
                'enable_arena'  => $this->enable_arena,
            ]);
        }

        Notification::make()->title('Configuración actualizada')->success()->send();
    }
}