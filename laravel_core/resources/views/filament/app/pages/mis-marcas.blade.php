<x-filament-panels::page>

    {{-- EL GRÁFICO SE CARGA AUTOMÁTICAMENTE DESDE PHP, NO LO PONEMOS AQUÍ PARA EVITAR DUPLICADOS --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        
        {{-- COLUMNA IZQUIERDA: Lista de Ejercicios --}}
        <div class="md:col-span-1 space-y-4">
            <h3 class="text-lg font-bold">Mis Ejercicios</h3>
            
            @forelse ($this->overviewData as $data)
                <div wire:click="selectExercise({{ $data->exercise_id }})"
                     class="cursor-pointer p-4 rounded-xl border transition-all duration-200 
                            {{ $selectedExerciseId === $data->exercise_id ? 'border-primary-500 bg-primary-50 dark:bg-primary-500/10 shadow-md' : 'border-gray-200 dark:border-white/10 hover:shadow-sm bg-white dark:bg-gray-900' }}">
                    
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-gray-100 dark:bg-white/5 rounded-lg text-gray-600 dark:text-gray-300">
                                @svg($data->icon_name, 'w-5 h-5')
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $data->exercise_name }}</span>
                        </div>
                        <span class="font-bold text-primary-600 dark:text-primary-400">{{ $data->weight }} kg</span>
                    </div>

                    {{-- Barra de progreso --}}
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2 mt-2 overflow-hidden">
                        <div class="bg-primary-500 h-2 rounded-full transition-all duration-500"
                             style="width: {{ $data->max > 0 ? ($data->weight / $data->max) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center p-6 text-gray-500 bg-white dark:bg-gray-900 rounded-xl border border-dashed border-gray-300 dark:border-white/20">
                    <x-heroicon-o-clipboard-document-list class="w-12 h-12 mx-auto text-gray-400 mb-2" />
                    <p>Aún no tienes marcas registradas.</p>
                </div>
            @endforelse
        </div>

        {{-- COLUMNA DERECHA: Detalle, Porcentajes e Historial --}}
        <div class="md:col-span-2">
            @if($selectedExerciseId && $this->recordData['name'])
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-white/10 p-6 shadow-sm">
                    
                    {{-- CABECERA DEL RÉCORD --}}
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-white/10">
                        <div class="p-3 bg-primary-100 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400 rounded-xl">
                             @svg($this->recordData['icon_name'], 'w-8 h-8')
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->recordData['name'] }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Récord Actual (1RM): <strong class="text-primary-600 dark:text-primary-400 text-lg">{{ $this->recordData['max_weight'] }} kg</strong></p>
                        </div>
                    </div>

                    {{-- NUEVO: TABLA DE PORCENTAJES --}}
                    @if($this->recordData['max_weight'] > 0)
                        <h3 class="text-sm font-bold mb-3 text-gray-600 dark:text-gray-300 uppercase tracking-wider">Calculadora de Porcentajes</h3>
                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-7 gap-2 mb-8">
                            @foreach([100, 95, 90, 85, 80, 75, 70, 65, 60, 55, 50, 45, 40] as $percent)
                                <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-lg p-2 flex flex-col items-center justify-center hover:bg-primary-50 hover:dark:bg-primary-500/10 hover:border-primary-200 dark:hover:border-primary-500/30 transition-colors">
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 font-bold">{{ $percent }}%</div>
                                    <div class="text-sm font-black text-gray-900 dark:text-white">
                                        {{ number_format($this->recordData['max_weight'] * ($percent / 100), 1) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- HISTORIAL DE LEVANTAMIENTOS --}}
                    <h3 class="text-sm font-bold mb-3 text-gray-600 dark:text-gray-300 uppercase tracking-wider">Historial de levantamientos</h3>
                    
                    <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($this->recordData['history'] as $record)
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center p-3 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-100 dark:border-white/10 hover:border-primary-200 dark:hover:border-primary-500/30 transition-colors group">
                                <div class="flex items-center gap-4 mb-2 sm:mb-0">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($record->achieved_at)->format('d M, Y') }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($record->achieved_at)->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between sm:justify-end gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-lg font-bold {{ $record->weight == $this->recordData['max_weight'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $record->weight }} kg
                                        </span>
                                        @if($record->weight == $this->recordData['max_weight'])
                                            <x-heroicon-s-star class="w-5 h-5 text-warning-400" />
                                        @else
                                            <div class="w-5 h-5"></div>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1 border-l border-gray-200 dark:border-gray-700 pl-3 ml-2">
                                        {{ ($this->editAction)(['record' => $record->id]) }}
                                        {{ ($this->deleteAction)(['record' => $record->id]) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No hay historial para este ejercicio.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="h-full min-h-[400px] flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-xl bg-gray-50 dark:bg-white/5">
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-full mb-4 shadow-sm">
                        <x-heroicon-o-cursor-arrow-rays class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Selecciona un ejercicio</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm">Haz clic en cualquier ejercicio de la lista izquierda para ver tu evolución, récords e historial detallado.</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
