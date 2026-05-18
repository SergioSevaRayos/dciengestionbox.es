@php
    $chartLabels = []; $chartData = [];
    foreach($records->reverse() as $record) {
        $chartLabels[] = \Carbon\Carbon::parse($record->date)->format('d/m/y');
        if (preg_match('/(\d+):(\d+)/', $record->score, $matches)) {
            $chartData[] = ($matches[1] * 60) + $matches[2];
        } else {
            preg_match('/[\d\.]+/', str_replace(',', '.', $record->score), $matches);
            $chartData[] = isset($matches[0]) ? (float) $matches[0] : 0;
        }
    }
@endphp

<div class="space-y-6 -mx-4 sm:mx-0">
    @if($records->isEmpty())
        <div class="text-center py-8 text-gray-500 italic px-4">No hay marcas registradas.</div>
    @else
        {{-- Gráfico: Más alto y con relleno lateral para no tocar bordes --}}
        <div class="px-4">
            <div class="w-full rounded-xl border border-gray-200 dark:border-white/10 p-4 bg-gray-50/50 dark:bg-white/[0.02]"
                 x-data="{
                    init() {
                        const render = () => {
                            Chart.defaults.color = '#9ca3af';
                            new Chart(this.$refs.canvas.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: @js($chartLabels),
                                    datasets: [{
                                        data: @js($chartData),
                                        borderColor: '#9333ea',
                                        backgroundColor: 'rgba(147, 51, 234, 0.1)',
                                        fill: true, tension: 0.3, borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#9333ea'
                                    }]
                                },
                                options: { 
                                    responsive: true, 
                                    maintainAspectRatio: false, 
                                    plugins: { legend: {display: false} },
                                    scales: { y: {display: false}, x: {grid: {display: false}, ticks: {font: {size: 10}}} }
                                }
                            });
                        }
                        if (typeof Chart === 'undefined') {
                            const s = document.createElement('script'); s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                            s.onload = render; document.head.appendChild(s);
                        } else { render(); }
                    }
                 }">
                <div class="h-44 sm:h-52"><canvas x-ref="canvas"></canvas></div>
            </div>
        </div>

        {{-- Tabla: De borde a borde en el slide-over --}}
        <div class="overflow-hidden border-y border-gray-200 dark:border-white/10 sm:rounded-xl sm:border sm:mx-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 font-bold uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Resultado</th>
                            <th class="px-2 py-3 text-center">Mod</th>
                            <th class="px-4 py-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @foreach($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-300 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($record->date)->format('d/m/y') }}</td>
                            <td class="px-4 py-3 font-black text-gray-900 dark:text-white whitespace-nowrap">{{ $record->score }}</td>
                            <td class="px-2 py-3 text-center">
                                <span class="inline-block px-2 py-0.5 text-[9px] font-bold rounded-md border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400">
                                    {{ $record->modality }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button onclick="confirm('¿Eliminar marca?') || event.stopImmediatePropagation()" 
                                        wire:click="borrarMarca({{ $record->id }})" 
                                        class="text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
