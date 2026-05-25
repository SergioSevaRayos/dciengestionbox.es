<x-filament-panels::page>
    <x-filament::section>
        <div class="prose max-w-none">
            <h2>{{ $record->name }}</h2>
            <p>Tipo: {{ $record->type === 'internal' ? 'Interna' : 'Abierta' }}</p>
            <p>Estado: {{ $record->is_active ? 'Activa' : 'Finalizada' }}</p>
        </div>
    </x-filament::section>
</x-filament-panels::page>
