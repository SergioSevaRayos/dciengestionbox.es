<x-filament-widgets::widget>
    <div x-data="{ isCollapsed: true }" class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        {{-- Cabecera / Pestaña --}}
        <div 
            @click="isCollapsed = !isCollapsed" 
            class="flex cursor-pointer items-center justify-between p-4 bg-gray-50/50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors"
        >
            <div class="flex items-center gap-2">
                <x-heroicon-o-fire class="h-5 w-5 text-danger-500" />
                <span class="text-sm font-bold text-gray-950 dark:text-white uppercase tracking-wider">
                    🚨 Pagos Pendientes
                </span>
            </div>
            
            <x-heroicon-m-chevron-down 
                class="h-5 w-5 text-gray-400 transition-transform duration-200" 
                ::class="{ 'rotate-180': !isCollapsed }"
            />
        </div>

        {{-- Contenido (Sin x-collapse para máxima velocidad) --}}
        <div 
            x-show="!isCollapsed" 
            x-cloak
            class="border-t border-gray-200 dark:border-white/10 p-2"
        >
            {{ $this->table }}
        </div>
    </div>
</x-filament-widgets::widget>
