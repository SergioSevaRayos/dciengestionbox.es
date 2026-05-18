<div 
    x-data="pwaInstaller()" 
    x-show="showButton" 
    x-cloak
    class="flex items-center"
>
    <button 
        @click="installApp" 
        class="flex items-center gap-2 px-3 py-1.5 text-sm font-bold text-white bg-primary-600 rounded-lg shadow-md hover:bg-primary-500 transition-all ring-1 ring-inset ring-primary-700/10"
    >
        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
        <span class="hidden sm:inline">Instalar App</span>
        <span class="sm:hidden">Instalar</span>
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

                if (this.isIos) {
                    this.showButton = true;
                }
            },

            async installApp() {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    const { outcome } = await this.deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        this.showButton = false;
                    }
                    this.deferredPrompt = null;
                } 
                else if (this.isIos) {
                    alert("🍎 En iPhone/iPad:\n\n1. Toca el icono de 'Compartir' (el cuadrado con la flecha hacia arriba) en la barra inferior de Safari.\n2. Desliza hacia abajo y selecciona 'Añadir a la pantalla de inicio'.");
                }
            }
        }))
    })
</script>
