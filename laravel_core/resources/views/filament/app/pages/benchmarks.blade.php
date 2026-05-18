<x-filament-panels::page>
    <div x-data="{ tab: 'heroes', search: '', filter: 'todos' }" class="space-y-6">
        
                <div class="grid grid-cols-2 md:flex md:flex-wrap gap-3 mb-6">
            <button x-on:click="tab = 'heroes'"
                    :class="tab === 'heroes' ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700'"
                    class="w-full md:w-auto py-3 md:py-2 px-2 md:px-6 rounded-xl text-sm font-bold transition-all text-center">
                Héroes
            </button>
            <button x-on:click="tab = 'girls'"
                    :class="tab === 'girls' ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700'"
                    class="w-full md:w-auto py-3 md:py-2 px-2 md:px-6 rounded-xl text-sm font-bold transition-all text-center">
                The Girls
            </button>
            <button x-on:click="tab = 'especiales'"
                    :class="tab === 'especiales' ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700'"
                    class="w-full md:w-auto py-3 md:py-2 px-2 md:px-6 rounded-xl text-sm font-bold transition-all text-center">
                Especiales
            </button>
            <button x-on:click="tab = 'qualifiers'"
                    :class="tab === 'qualifiers' ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700'"
                    class="w-full md:w-auto py-3 md:py-2 px-2 md:px-6 rounded-xl text-sm font-bold transition-all text-center">
                Qualifiers
            </button>
        </div>

        <div class="space-y-4">
            <div class="max-w-md">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" x-model="search" placeholder="Buscar WOD..." />
                </x-filament::input.wrapper>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="f in ['todos', 'For Time', 'AMRAP', 'EMOM', 'Tabata']">
                    <button @click="filter = f" 
                            :class="filter === f ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700'"
                            class="px-4 py-1.5 text-xs font-bold uppercase rounded-full transition-all"
                            x-text="f">
                    </button>
                </template>
            </div>
        </div>

        @foreach(['heroes' => $heroes, 'girls' => $girls, 'especiales' => $especiales, 'qualifiers' => $qualifiers] as $key => $collection)
        <div x-show="tab === '{{ $key }}'" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            @foreach($collection as $item)
                <div x-show="(filter === 'todos' || '{{ $item['modalidad'] }}'.includes(filter)) && (search === '' || '{{ strtolower($item['nombre']) }}'.includes(search.toLowerCase()))">
                    <x-filament::section>
                        <x-slot name="heading">
                            <span class="text-xl font-black not-italic text-gray-900 dark:text-white uppercase tracking-tighter flex items-center gap-2">{{ $item['nombre'] }} @if(in_array($item['nombre'], $completedWods)) <span title="¡Marca registrada!">🏆</span> @endif</span>
                        </x-slot>
                        <x-slot name="headerEnd">
                            <x-filament::badge color="primary">{{ $item['modalidad'] }}</x-filament::badge>
                        </x-slot>

                        <ul class="flex flex-col gap-3 mt-2 mb-6">
                            @foreach(explode(', ', $item['descripcion']) as $line)
                                <li class="flex items-start gap-3">
                                    <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5 text-primary-500 shrink-0 mt-0.5" />
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-relaxed">{{ trim($line) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="border-t border-gray-200 dark:border-white/10 pt-4 flex gap-3 justify-end">
                            <x-filament::button wire:click="mountAction('verHistorial', { wod: '{{ $item['nombre'] }}' })" color="gray" class="flex-1 sm:flex-none">Historial</x-filament::button>
                            <x-filament::button wire:click="mountAction('registrarMarca', { wod: '{{ $item['nombre'] }}' })" color="primary" class="flex-1 sm:flex-none">Añadir Marca</x-filament::button>
                        </div>
                    </x-filament::section>
                </div>
            @endforeach
        </div>
        @endforeach

    </div>
</x-filament-panels::page>
