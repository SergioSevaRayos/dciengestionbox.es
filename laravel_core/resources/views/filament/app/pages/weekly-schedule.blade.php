<x-filament-panels::page>
    <div class="max-w-3xl mx-auto w-full space-y-10">
        @php
            $classesByDay = $this->classesByDay;
            $nombresDias = [
                'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'
            ];
            $meses = [
                'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo', 'April' => 'Abril',
                'May' => 'Mayo', 'June' => 'Junio', 'July' => 'Julio', 'August' => 'Agosto',
                'September' => 'Septiembre', 'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
            ];
        @endphp

        @for ($i = 0; $i < 7; $i++)
            @php
                $currentDay = now()->addDays($i);
                $dateKey = $currentDay->format('Y-m-d');
                $dayClasses = $classesByDay->get($dateKey, collect());
                $nombreDia = $nombresDias[$currentDay->format('l')];
                $nombreMes = $meses[$currentDay->format('F')];
                $esHoy = $currentDay->isToday();
            @endphp

            <div class="relative">
                <div class="py-2 mb-3 border-b border-gray-200 dark:border-gray-700 flex items-center">
                    <h2 class="text-lg text-gray-900 dark:text-white">
                        <span class="font-bold">{{ $nombreDia }}</span>
                        <span class="text-gray-500 dark:text-gray-400 font-light ml-1">{{ $currentDay->format('d') }} de {{ $nombreMes }}</span>
                    </h2>
                    @if($esHoy)
                        <span class="ml-3 px-2 py-0.5 text-[10px] font-bold bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 rounded uppercase tracking-wider">Hoy</span>
                    @endif
                </div>

                @if($dayClasses->isEmpty())
                    <div class="py-3 pl-2 text-sm text-gray-500 dark:text-gray-400 font-light italic">
                        Sin disciplinas programadas.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($dayClasses as $session)
                            {{-- Añadimos x-data para controlar el desplegable individual de cada sesión --}}
                            <div x-data="{ openWod: false }" class="flex flex-col bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 shadow-sm overflow-hidden">
                                
                                <div class="flex items-center p-4">
                                    <div class="w-32 flex-shrink-0 border-r border-gray-200 dark:border-gray-700 pr-4">
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white tracking-widest">
                                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                        </span>
                                    </div>

                                    <div class="pl-8 flex-1 flex flex-col justify-center">
                                        <span class="text-xl font-medium text-gray-900 dark:text-white tracking-tight">
                                            {{ $session->classType->name ?? 'Disciplina no especificada' }}
                                        </span>

                                        {{-- BOTÓN DINÁMICO: Solo si es visible y tiene contenido --}}
                                        @if($session->is_workout_visible && $session->workout_id)
                                            <button @click="openWod = !openWod" class="flex items-center gap-1 mt-1 text-xs font-bold text-primary-600 dark:text-primary-400 hover:opacity-75 transition-opacity uppercase tracking-tighter">
                                                <x-heroicon-o-document-text class="w-4 h-4" />
                                                <span x-text="openWod ? 'Ocultar Entrenamiento' : 'Ver Entrenamiento'"></span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- DESPLEGABLE DEL WOD --}}
                                <div x-show="openWod" x-collapse x-cloak class="border-t border-gray-100 dark:border-white/5 bg-gray-50/30 dark:bg-white/5">
                                    <div class="p-6 text-sm text-gray-800 dark:text-gray-200 
                                        [&_p]:mb-3 
                                        [&_ul]:list-disc [&_ul]:ml-5 [&_ul]:mb-3
                                        [&_strong]:text-gray-950 dark:[&_strong]:text-white">
                                        {!! $session->workout?->content !!}
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endfor
    </div>
</x-filament-panels::page>
