<x-filament-panels::page>
<div x-data="{}" class="space-y-6">

    {{-- CABECERA --}}
    <x-filament::section>
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <x-filament::badge color="{{ $competition->type === 'open' ? 'success' : 'warning' }}">
                        {{ $competition->type === 'open' ? '🌍 Open' : '🏠 Interna' }}
                    </x-filament::badge>
                    @if($competition->is_active)
                        <x-filament::badge color="success">Activa</x-filament::badge>
                    @else
                        <x-filament::badge color="gray">Inactiva</x-filament::badge>
                    @endif
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">{{ $competition->name }}</h2>
                @if($competition->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $competition->description }}</p>
                @endif
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                    {{ $competition->start_date->format('d/m/Y') }}
                    @if($competition->end_date) — {{ $competition->end_date->format('d/m/Y') }} @endif
                    · <strong>{{ $competition->teams->count() }}</strong> equipos
                    · <strong>{{ $competition->wods->count() }}</strong> WODs
                </p>
            </div>
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- EQUIPOS INSCRITOS --}}
        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <x-filament::icon icon="heroicon-m-users" class="w-4 h-4"/>
                Equipos ({{ $competition->teams->count() }})
            </h3>

            @forelse($competition->teams as $team)
            <x-filament::section>
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="font-black text-gray-900 dark:text-white">{{ $team->name }}</span>
                            <x-filament::badge color="gray" size="sm">{{ $team->getFormatLabel() }}</x-filament::badge>
                            <x-filament::badge color="primary" size="sm">{{ $team->getCategoryLabel() }}</x-filament::badge>
                        </div>
                        @if($competition->type === 'open')
                            <p class="text-xs text-primary-500 font-semibold mb-1">{{ $team->gym->name }}</p>
                        @endif
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($team->members as $member)
                            <span class="inline-flex items-center gap-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded-full">
                                {{ $member->name }}
                            </span>
                            @endforeach
                        </div>
                        @if($team->results->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/10 space-y-1.5">
                            @foreach($team->results->sortBy(fn($r) => $r->wod->order ?? 0) as $result)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400 truncate">{{ $result->wod->name ?? '—' }}</span>
                                <div class="flex items-center gap-2 shrink-0 ml-2">
                                    <span class="font-mono font-black text-gray-900 dark:text-white">{{ $result->result_value }}</span>
                                    <x-filament::badge size="sm" color="gray">{{ $result->getCategoryLabel() }}</x-filament::badge>
                                    @if($result->rank_in_wod)
                                    <x-filament::badge size="sm" color="warning">#{{ $result->rank_in_wod }}</x-filament::badge>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <button
                        wire:click="eliminarEquipo({{ $team->id }})"
                        wire:confirm="¿Eliminar este equipo y todos sus resultados?"
                        class="text-gray-300 dark:text-gray-600 hover:text-danger-500 transition shrink-0 mt-0.5">
                        <x-filament::icon icon="heroicon-m-trash" class="w-4 h-4"/>
                    </button>
                </div>
            </x-filament::section>
            @empty
            <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
                <p class="text-sm text-gray-400">Sin equipos inscritos aún</p>
            </div>
            @endforelse
        </div>

        {{-- LEADERBOARD + RANKING --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- SELECTOR WODs --}}
            @if($competition->wods->count() > 0)
            <div class="flex gap-2 flex-wrap">
                @foreach($competition->wods as $wod)
                <button
                    wire:click="selectWod({{ $wod->id }})"
                    class="px-5 py-2 rounded-xl text-sm font-bold border-2 transition-all
                        {{ $activeWod?->id === $wod->id
                            ? 'bg-primary-600 text-white border-primary-600 shadow-md'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-primary-400' }}">
                    {{ $wod->name }}
                    <span class="text-xs opacity-70 ml-1">{{ $wod->getScoreTypeLabel() }}</span>
                </button>
                @endforeach
            </div>
            @endif

            {{-- LEADERBOARD WOD --}}
            @if($activeWod)
            <x-filament::section>
                <x-slot name="heading">
                    <span class="flex items-center gap-2">
                        {{ $activeWod->name }}
                        <x-filament::badge color="primary" size="sm">{{ $activeWod->getScoreTypeLabel() }}</x-filament::badge>
                        <x-filament::badge color="gray" size="sm">{{ $activeWod->lowerIsBetter() ? 'menor gana' : 'mayor gana' }}</x-filament::badge>
                    </span>
                </x-slot>
                @if($activeWod->description)
                <x-slot name="description">{{ $activeWod->description }}</x-slot>
                @endif

                @if(count($leaderboard) === 0)
                <div class="text-center py-10">
                    <p class="text-sm text-gray-400">Sin resultados aún para este WOD</p>
                </div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-white/10 -mx-6">
                    @foreach($leaderboard as $i => $result)
                    <div class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <div class="w-8 text-center shrink-0">
                            @if($i === 0) <span class="text-2xl">🥇</span>
                            @elseif($i === 1) <span class="text-2xl">🥈</span>
                            @elseif($i === 2) <span class="text-2xl">🥉</span>
                            @else <span class="text-sm font-black text-gray-400">#{{ $i+1 }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $result->team->name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $result->team->members->pluck('name')->join(' · ') }}
                                @if($competition->type === 'open')
                                    · <span class="text-primary-500">{{ $result->team->gym->name }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right">
                                <span class="font-black font-mono text-xl text-gray-900 dark:text-white">{{ $result->result_value }}</span>
                                <p class="text-xs text-gray-400">{{ $result->getCategoryLabel() }}</p>
                            </div>
                            @if($result->points)
                            <x-filament::badge color="warning">{{ $result->points }} pts</x-filament::badge>
                            @endif
                            <button
                                wire:click="eliminarResultado({{ $result->id }})"
                                wire:confirm="¿Eliminar este resultado?"
                                class="text-gray-300 dark:text-gray-600 hover:text-danger-500 transition">
                                <x-filament::icon icon="heroicon-m-x-mark" class="w-4 h-4"/>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </x-filament::section>
            @endif

            {{-- CLASIFICACIÓN GENERAL --}}
            @if($generalRanking->count() > 0)
            <x-filament::section>
                <x-slot name="heading">
                    <span class="flex items-center gap-2 text-warning-600 dark:text-warning-400">
                        <x-filament::icon icon="heroicon-m-trophy" class="w-5 h-5"/>
                        Clasificación General
                    </span>
                </x-slot>
                <div class="divide-y divide-gray-100 dark:divide-white/10 -mx-6">
                    @foreach($generalRanking as $i => $team)
                    <div class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <div class="w-8 text-center shrink-0">
                            @if($i === 0) <span class="text-2xl">🥇</span>
                            @elseif($i === 1) <span class="text-2xl">🥈</span>
                            @elseif($i === 2) <span class="text-2xl">🥉</span>
                            @else <span class="text-sm font-black text-gray-400">#{{ $i+1 }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $team->name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                @if($competition->type === 'open')
                                    <span class="text-primary-500">{{ $team->gym->name }}</span> ·
                                @endif
                                {{ $team->members->pluck('name')->join(' · ') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-black text-3xl text-warning-500">{{ $team->total_points }}</span>
                            <p class="text-xs text-gray-400">puntos</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>
            @endif

        </div>
    </div>
</div>
</x-filament-panels::page>