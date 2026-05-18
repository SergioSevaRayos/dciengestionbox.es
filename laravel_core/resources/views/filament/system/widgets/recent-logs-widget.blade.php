<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-command-line" heading="Monitor de Sistema" description="Últimos errores registrados en tiempo real">
        <div class="bg-gray-950 text-green-400 p-4 rounded-lg font-mono text-xs md:text-sm overflow-x-auto shadow-inner border border-gray-800">
            @forelse($this->logs as $log)
                <div class="mb-2 border-b border-gray-800 pb-2 break-all">
                    <span class="text-red-500 font-bold">⚠️ [ERROR]</span> 
                    <span class="text-gray-300">{{ $log }}</span>
                </div>
            @empty
                <div class="text-gray-500 text-center py-6 flex flex-col items-center justify-center">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-green-500 mb-2"/>
                    <span>Sistemas en línea. No se han detectado errores recientes.</span>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4 flex justify-end">
            <x-filament::button tag="a" href="/log-viewer" color="gray" size="sm" icon="heroicon-m-arrow-top-right-on-square">
                Abrir Visor de Logs Avanzado
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
