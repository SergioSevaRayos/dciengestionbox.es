<div class="flex items-center gap-x-3" wire:poll.10s="actualizarDatos">
    <div class="flex items-center gap-x-3 me-3 px-4 py-1.5 bg-gray-50 dark:bg-white/5 rounded-full border border-gray-200 dark:border-white/10">
        <div class="flex flex-col items-end justify-center">
            <span class="text-[9px] font-black uppercase tracking-tighter text-gray-500 leading-none mb-0.5">Créditos</span>
            <span class="text-sm font-bold leading-none {{ $color }} transition-colors duration-300">
                {{ $credits }}
            </span>
        </div>
        <x-heroicon-m-ticket class="w-5 h-5 text-gray-400" />
    </div>
</div>
