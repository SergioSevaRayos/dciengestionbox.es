<div 
    x-data="pwaInstaller()" 
    x-show="showButton" 
    x-cloak
    class="mb-6 p-4 rounded-xl border border-primary-500/20 bg-primary-500/10 flex items-center justify-between"
>
    <div class="flex items-center gap-3 text-left">
        <div class="p-2 bg-primary-500 rounded-lg text-white shadow-md">
            <x-heroicon-s-device-phone-mobile class="w-6 h-6" />
        </div>
        <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Instala la App Oficial</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400">Acceso más rápido y sin navegador.</p>
        </div>
    </div>
    <button 
        @click="installApp" 
        class="px-4 py-2 text-xs font-bold text-white bg-primary-600 rounded-lg shadow-md hover:bg-primary-500 transition-all"
    >
        Instalar
    </button>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pwaInstaller', () => ({
            deferredPrompt: null,
            showButton: false,
            isIos: /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
            isStandalone: window.matchMedia('(display-mode: standalone)').matches,

            init() {
                if (this.isStandalone) return;
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.showButton = true;
                });
                if (this.isIos) this.showButton = true;
            },

            async installApp() {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    const { outcome } = await this.deferredPrompt.userChoice;
                    if (outcome === 'accepted') this.showButton = false;
                    this.deferredPrompt = null;
                } else if (this.isIos) {
                    alert("🍎 En iPhone/iPad:\n\n1. Toca 'Compartir' (cuadrado con flecha) en la barra inferior.\n2. Selecciona 'Añadir a la pantalla de inicio'.");
                }
            }
        }))
    })
</script>
