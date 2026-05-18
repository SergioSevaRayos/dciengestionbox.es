<x-filament-widgets::widget>
    <div wire:poll.5s="cargarBonos">
        <x-filament::section>
            <x-slot name="heading">Resumen de tus Clases</x-slot>

            @if(count($activePackages) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- BONOS ACTIVOS --}}
                    @foreach($activePackages as $bono)
                        @php
                            $urgente = ($bono['days_left'] <= 3 || $bono['remaining'] <= 3);
                        @endphp
                        <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-xl border-l-4 {{ $urgente ? 'border-red-500 bg-red-50/10' : 'border-primary-500' }} shadow-sm transition-all">
                            <div class="flex justify-between items-start">
                                <h4 class="text-md font-bold text-gray-900 dark:text-white">{{ $bono['name'] }}</h4>
                                @if($urgente && !$bono['is_requested'] && !$bono['already_renewed'])
                                    <span class="text-[10px] font-bold text-red-600 animate-pulse uppercase">¡Agotándose!</span>
                                @endif
                            </div>

                            <div class="flex justify-between items-end mt-4">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase">Clases restantes</p>
                                    <span class="text-2xl font-black {{ $bono['remaining'] <= 3 ? 'text-red-600' : 'text-primary-600' }}">{{ $bono['remaining'] }}</span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 uppercase">Válido hasta</p>
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $bono['expires'] }}</p>

                                    <div class="mt-2">
                                        @if($bono['is_requested'])
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-lg text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                                <x-filament::icon icon="heroicon-m-check-circle" class="w-4 h-4" /> Solicitud tramitada
                                            </span>
                                        @elseif($bono['already_renewed'])
                                             <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                                <x-filament::icon icon="heroicon-m-arrow-path" class="w-4 h-4" /> Bono renovado
                                            </span>
                                        @elseif($urgente)
                                            {{ $this->requestRenewalAction }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- DISEÑO LÍQUIDO PARA 0 CRÉDITOS --}}
                <div class="flex flex-col items-center justify-center p-4 md:p-6 text-center w-full">
                    <div class="mb-4 bg-red-100 dark:bg-red-500/20 p-4 rounded-full shadow-sm">
                        <x-heroicon-o-ticket class="w-8 h-8 md:w-10 md:h-10 text-red-600 dark:text-red-400" />
                    </div>
                    
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Bono Agotado
                    </h3>
                    
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                        No tienes clases disponibles. Renueva tu bono para seguir reservando y no perder el ritmo.
                    </p>
                    
                    <div class="w-full flex justify-center">
                        {{ $this->requestRenewalAction }}
                    </div>
                </div>
            @endif

        </x-filament::section>
    </div>
    <x-filament-actions::modals />
</x-filament-widgets::widget>
