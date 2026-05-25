<x-filament::section>
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold">Mis Competiciones Activas</h3>
    </div>
    @if ($competitions->isEmpty())
        <p class="text-gray-500">No estás inscrito en ninguna competición activa.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            @foreach ($competitions as $comp)
                <a href="{{ \Filament\Facades\Filament::getTenant() ? url('admin/' . \Filament\Facades\Filament::getTenant()->slug . '/view-competition/' . $comp->id) : '#' }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                    <h4 class="font-semibold">{{ $comp->name }}</h4>
                    <p class="text-sm text-gray-600">{{ $comp->type === 'internal' ? 'Interna' : 'Abierta' }}</p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($comp->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($comp->end_date)->format('d/m/Y') }}</p>
                </a>
            @endforeach
        </div>
    @endif
</x-filament::section>
