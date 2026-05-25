<x-filament-panels::page>
<div class="space-y-6">

    @if($competitions->isEmpty())
    <x-filament::section>
        <div class="text-center py-12">
            <x-filament::icon icon="heroicon-o-trophy" class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600"/>
            <p class="text-lg font-bold text-gray-900 dark:text-white">Sin competiciones activas</p>
            <p class="text-sm text-gray-500 mt-1">Tu box activará una próximamente.</p>
        </div>
    </x-filament::section>
    @else

    @if($competitions->count() > 1)
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:4px">
        @foreach($competitions as $comp)
        <button wire:click="selectCompetition({{ $comp->id }})"
            style="{{ $activeCompetition?->id === $comp->id
                ? 'background:#d97706;color:#fff;border:2px solid #d97706;font-weight:800;'
                : 'background:transparent;color:#9ca3af;border:2px solid #374151;font-weight:700;' }}
                padding:8px 18px;border-radius:10px;font-size:0.875rem;cursor:pointer;transition:all .2s;">
            {{ $comp->name }}
            @if($comp->type === 'open') 🌍@endif
        </button>
        @endforeach
    </div>
    @endif

    @if($activeCompetition)

    {{-- CABECERA --}}
    <x-filament::section>
        <div class="flex items-center gap-2 mb-2 flex-wrap">
            <x-filament::badge color="{{ $activeCompetition->type === 'open' ? 'success' : 'warning' }}">
                {{ $activeCompetition->type === 'open' ? '🌍 Open' : '🏠 Interna' }}
            </x-filament::badge>
            <x-filament::badge color="success">Activa</x-filament::badge>
        </div>
        <h2 class="text-2xl font-black text-gray-900 dark:text-white">{{ $activeCompetition->name }}</h2>
        @if($activeCompetition->description)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $activeCompetition->description }}</p>
        @endif
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            {{ $activeCompetition->start_date->format('d/m/Y') }}
            @if($activeCompetition->end_date) — {{ $activeCompetition->end_date->format('d/m/Y') }} @endif
            · {{ $activeCompetition->teams->count() }} participantes
        </p>
    </x-filament::section>

    {{-- TABS --}}
    <div x-data="{ tab: 'leaderboard' }">

        {{-- TAB HEADERS --}}
        <div class="flex border-b border-gray-200 dark:border-white/10 mb-6">
            <button @click="tab = 'leaderboard'"
                :class="tab === 'leaderboard'
                    ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-bold'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                class="px-5 py-3 text-sm transition-all -mb-px flex items-center gap-2">
                📋 Leaderboard por WOD
            </button>
            <button @click="tab = 'ranking'"
                :class="tab === 'ranking'
                    ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-bold'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                class="px-5 py-3 text-sm transition-all -mb-px flex items-center gap-2">
                🏆 Clasificación General
            </button>
        </div>

        {{-- ======================== TAB LEADERBOARD ======================== --}}
        <div x-show="tab === 'leaderboard'" class="space-y-4">

            {{-- SELECTOR WODs --}}
            @if($activeCompetition->wods->where('is_active', true)->count() > 0)
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                @foreach($activeCompetition->wods->where('is_active', true) as $wod)
                <button wire:click="selectWod({{ $wod->id }})"
                    style="{{ $activeWod?->id === $wod->id
                        ? 'background:#d97706;color:#fff;border:2px solid #d97706;font-weight:800;'
                        : 'background:transparent;color:#9ca3af;border:2px solid #374151;font-weight:700;' }}
                        padding:6px 16px;border-radius:10px;font-size:0.8rem;cursor:pointer;transition:all .2s;">
                    {{ $wod->name }}
                </button>
                @endforeach
            </div>
            @endif

            {{-- SELECTOR FORMATO --}}
            <div class="flex gap-2 flex-wrap">
                @foreach(['all' => 'Todos', 'individual' => 'Individual', 'pairs' => 'Parejas', 'team' => 'Equipo'] as $fmt => $label)
                <button wire:click="selectFormat('{{ $fmt }}')"
                    style="{{ $activeFormat === $fmt
                        ? 'background:#d97706;color:#fff;border:2px solid #d97706;font-weight:800;'
                        : 'background:transparent;color:#9ca3af;border:2px solid #374151;font-weight:700;' }}
                        padding:5px 14px;border-radius:8px;font-size:0.75rem;cursor:pointer;transition:all .2s;">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            @if($activeWod)
            @php
                $fmtLabels = ['individual' => 'Individual', 'pairs' => 'Parejas', 'team' => 'Equipo'];
                $byFmt = $leaderboard->groupBy(fn($r) => $r->team->format);
                $fmtsToShow = $activeFormat === 'all' ? ['individual','pairs','team'] : [$activeFormat];
                $hasAny = collect($fmtsToShow)->contains(fn($f) => isset($byFmt[$f]) && $byFmt[$f]->count() > 0);
            @endphp

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

                @if(!$hasAny)
                <div class="text-center py-10">
                    <x-filament::icon icon="heroicon-o-clock" class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600"/>
                    <p class="text-sm text-gray-400">Sin resultados aún</p>
                </div>
                @else
                @foreach($fmtsToShow as $fmt)
                    @if(isset($byFmt[$fmt]) && $byFmt[$fmt]->count() > 0)
                        @if($activeFormat === 'all')
                        <div class="py-2 mb-1 border-b border-gray-100 dark:border-white/10">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ $fmtLabels[$fmt] }}</span>
                        </div>
                        @endif
                        <div class="divide-y divide-gray-100 dark:divide-white/10 -mx-6 mb-4">
                            @foreach($byFmt[$fmt]->values() as $i => $result)
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
                                        @if($activeCompetition->type === 'open') · <span class="text-primary-500">{{ $result->team->gym->name }}</span>@endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <div class="text-right">
                                        <span class="font-black font-mono text-xl text-gray-900 dark:text-white">{{ $result->result_value }}</span>
                                        <p class="text-xs text-gray-400">{{ $result->getCategoryLabel() }}</p>
                                    </div>
                                    @if($result->points)
                                    <x-filament::badge color="warning">{{ $result->points }}p</x-filament::badge>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
                @endif
            </x-filament::section>
            @endif
        </div>

        {{-- ======================== TAB CLASIFICACIÓN GENERAL ======================== --}}
        <div x-show="tab === 'ranking'" class="space-y-4">

            {{-- SELECTOR FORMATO --}}
            <div class="flex gap-2 flex-wrap">
                @foreach(['all' => 'Todos', 'individual' => 'Individual', 'pairs' => 'Parejas', 'team' => 'Equipo'] as $fmt => $label)
                <button wire:click="selectFormat('{{ $fmt }}')"
                    style="{{ $activeFormat === $fmt
                        ? 'background:#d97706;color:#fff;border:2px solid #d97706;font-weight:800;'
                        : 'background:transparent;color:#9ca3af;border:2px solid #374151;font-weight:700;' }}
                        padding:5px 14px;border-radius:8px;font-size:0.75rem;cursor:pointer;transition:all .2s;">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            @php
                $fmtsRanking = $activeFormat === 'all'
                    ? ['individual' => 'Individual', 'pairs' => 'Parejas', 'team' => 'Equipos']
                    : [$activeFormat => ['individual'=>'Individual','pairs'=>'Parejas','team'=>'Equipos'][$activeFormat]];
                $anyRanking = collect($fmtsRanking)->keys()->contains(fn($f) => $generalRanking[$f]->count() > 0);
            @endphp

            @if(!$anyRanking)
            <x-filament::section>
                <div class="text-center py-10">
                    <x-filament::icon icon="heroicon-o-trophy" class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600"/>
                    <p class="text-sm text-gray-400">Sin clasificación disponible aún</p>
                </div>
            </x-filament::section>
            @else
            @foreach($fmtsRanking as $fmt => $label)
                @if($generalRanking[$fmt]->count() > 0)
                <x-filament::section>
                    <x-slot name="heading">
                        <span class="flex items-center gap-2 text-warning-600 dark:text-warning-400">
                            <x-filament::icon icon="heroicon-m-trophy" class="w-4 h-4"/>
                            {{ $label }}
                        </span>
                    </x-slot>
                    <div class="divide-y divide-gray-100 dark:divide-white/10 -mx-6">
                        @foreach($generalRanking[$fmt] as $i => $team)
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
                                <p class="text-xs text-gray-500 truncate">{{ $team->members->pluck('name')->join(' · ') }}</p>
                                @if($activeCompetition->type === 'open')
                                <p class="text-xs text-primary-500">{{ $team->gym->name }}</p>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-3xl text-warning-500">{{ $team->total_points }}</span>
                                <p class="text-xs text-gray-400">pts</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-filament::section>
                @endif
            @endforeach
            @endif
        </div>

    </div>{{-- fin tabs --}}
    @endif
    @endif
</div>
</x-filament-panels::page>