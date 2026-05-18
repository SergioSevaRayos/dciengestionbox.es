<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-black uppercase tracking-tighter italic text-gray-900 dark:text-white">
                Próximas Clases (24h)
            </h2>
            <x-heroicon-o-clock class="w-5 h-5 text-primary-500 animate-pulse" />
        </div>

        <div class="space-y-4">
            @forelse($this->sessions as $session)
                @php
                    $startTime = \Carbon\Carbon::parse($session->start_time);
                    $nombresDias = [
                        'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 
                        'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'
                    ];
                    $diaSemana = $nombresDias[$startTime->format('l')];
                    $diaMes = $startTime->format('d');
                    $mes = strtoupper($startTime->translatedFormat('M'));
                    $hora = $startTime->format('H:i');
                @endphp

                <div class="relative overflow-hidden bg-white dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 shadow-sm hover:border-primary-500 transition-all group">
                    <div class="flex items-center gap-5">
                        <div class="flex flex-col items-center justify-center bg-gray-900 dark:bg-black text-white rounded-xl py-2 px-4 min-w-[70px] shadow-lg group-hover:bg-primary-600 transition-colors">
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-70">{{ substr($diaSemana, 0, 3) }}</span>
                            <span class="text-2xl font-black leading-none my-1">{{ $diaMes }}</span>
                            <span class="text-[10px] font-black opacity-70">{{ $mes }}</span>
                        </div>

                        <div class="flex-1">
                            <div class="flex flex-col">
                                <span class="text-3xl font-black text-black dark:text-white tracking-tighter leading-none mb-1">
                                    {{ $hora }}
                                </span>
                                <span class="text-xs font-black uppercase text-primary-600 dark:text-primary-400 tracking-widest italic">
                                    {{ $session->classType->name ?? 'ENTRENAMIENTO' }}
                                </span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Aforo</div>
                            <div class="text-sm font-black text-black dark:text-white">
                                {{ $session->bookings_count }} / {{ $session->capacity }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-10 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-2xl opacity-50">
                    <x-heroicon-o-calendar-days class="w-8 h-8 text-gray-400 mb-2" />
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">No hay sesiones hoy</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
