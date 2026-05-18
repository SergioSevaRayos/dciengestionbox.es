<x-filament-panels::page>
    <div class="space-y-6">
        @if($tenant->is_subscribed)
            <div class="p-6 bg-success-50 dark:bg-success-500/10 border border-success-200 dark:border-success-500/20 rounded-xl flex items-start gap-4">
                <x-heroicon-o-shield-check class="w-8 h-8 text-success-600 dark:text-success-400 flex-shrink-0" />
                <div>
                    <h3 class="text-lg font-bold text-success-800 dark:text-success-400">Suscripción Activa</h3>
                    <p class="text-success-700 dark:text-success-500 text-sm mt-1">Tu gimnasio tiene acceso completo a todas las funciones de la plataforma. Tu próximo cobro se realizará automáticamente a través de Stripe.</p>
                </div>
            </div>
        @else
            <div class="p-6 bg-danger-50 dark:bg-danger-500/10 border border-danger-200 dark:border-danger-500/20 rounded-xl flex items-start gap-4">
                <x-heroicon-o-lock-closed class="w-8 h-8 text-danger-600 dark:text-danger-400 flex-shrink-0" />
                <div>
                    <h3 class="text-lg font-bold text-danger-800 dark:text-danger-400">Plataforma Bloqueada (Modo Lectura)</h3>
                    <p class="text-danger-700 dark:text-danger-500 text-sm mt-1">Actualmente puedes navegar por la plataforma y ver tus datos históricos, pero <strong>no puedes añadir clases, registrar alumnos ni realizar cobros</strong>. Activa tu suscripción en la parte superior para desbloquear tu cuenta.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                
                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm relative overflow-hidden">
                    <h4 class="text-xl font-black mb-2">Suscripción WebApp</h4>
                    <div class="text-4xl font-black mb-1 text-[#f59e0b]">10€<span class="text-lg text-gray-500 font-medium">/mes</span></div>
                    <div class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1">Primer mes de prueba gratuito</div>
                    <div class="text-[10px] text-gray-500 dark:text-gray-400 mb-6 italic">* 10€/mes durante el 1º año. Después 50€/mes.</div>
                    <ul class="space-y-2 mb-6 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2"><x-heroicon-o-check class="w-5 h-5 text-success-500" /> Cobros directos íntegros</li>
                        <li class="flex items-center gap-2"><x-heroicon-o-check class="w-5 h-5 text-success-500" /> Sin comisiones por alumno</li>
                        <li class="flex items-center gap-2"><x-heroicon-o-check class="w-5 h-5 text-success-500" /> Usuarios y Clases ilimitadas</li>
                        <li class="flex items-center gap-2"><x-heroicon-o-check class="w-5 h-5 text-success-500" /> App Web para alumnos</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
