<x-filament-widgets::widget>
    <div x-data="{ isCollapsed: true }" class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden text-left">
        
        {{-- Cabecera --}}
        <div 
            @click="isCollapsed = !isCollapsed" 
            class="flex cursor-pointer items-center justify-between p-4 bg-gray-50/50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-all"
        >
            <div class="flex items-center gap-3 text-left">
                <span class="text-xl">🏋️‍♂️</span>
                <div>
                    <h2 class="text-sm font-bold text-gray-950 dark:text-white uppercase tracking-tight">Gestión de Sala</h2>
                    <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest">Panel de Entrenamientos (WODs)</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if($missingWods->count() > 0)
                    <x-filament::badge color="danger" size="sm" pulse>Pendientes</x-filament::badge>
                @endif
                <x-heroicon-m-chevron-down 
                    class="h-5 w-5 text-gray-400 transition-transform duration-300" 
                    ::class="{ 'rotate-180': !isCollapsed }"
                />
            </div>
        </div>

        {{-- Contenido --}}
        <div 
            x-show="!isCollapsed" 
            x-cloak
            class="border-t border-gray-200 dark:border-white/10 p-6 space-y-8"
        >
            @if($missingWods->count() > 0)
                <div>
                    <h3 class="text-xs font-bold text-danger-600 dark:text-danger-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-heroicon-s-exclamation-circle class="h-4 w-4" />
                        Programación Faltante
                    </h3>
                    
                    <div class="space-y-2">
                        @foreach($missingWods as $session)
                            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                                <div class="flex flex-col text-left">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ $session->start_time->format('d M') }} <span class="text-gray-400 mx-1">|</span> {{ $session->start_time->format('H:i') }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $session->classType->name }}</span>
                                </div>
                                <x-filament::button 
                                    tag="a" 
                                    href="{{ route('filament.admin.resources.gym-sessions.edit', ['tenant' => filament()->getTenant(), 'record' => $session]) }}"
                                    color="gray"
                                    size="xs"
                                    icon="heroicon-m-pencil-square"
                                    outlined
                                >
                                    REDACTAR
                                </x-filament::button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <h3 class="text-xs font-bold text-primary-600 dark:text-primary-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <x-heroicon-s-fire class="h-4 w-4" />
                    WODs Activos (Hoy)
                </h3>

                @if($todaysWods->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($todaysWods as $wod)
                            <div class="rounded-xl p-6 border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 shadow-sm">
                                <div class="flex items-start justify-between mb-4 border-b border-gray-100 dark:border-white/5 pb-3">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-black text-primary-600 dark:text-primary-400 uppercase tracking-tighter">
                                                {{ $wod->start_time->format('H:i') }} | {{ $wod->classType->name }}
                                            </span>
                                            <x-heroicon-s-check-badge class="h-4 w-4 text-primary-500" />
                                        </div>
                                        @if($wod->workout?->name)
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">
                                                {{ $wod->workout->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed 
                                    [&_p]:mb-3 [&_strong]:text-gray-950 dark:[&_strong]:text-white
                                    [&_ul]:list-disc [&_ul]:ml-5 [&_ul]:space-y-1">
                                    {!! $wod->workout?->content !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 rounded-xl border border-dashed border-gray-300 dark:border-white/10">
                        <p class="text-sm text-gray-400 italic">No hay entrenamientos para hoy.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
