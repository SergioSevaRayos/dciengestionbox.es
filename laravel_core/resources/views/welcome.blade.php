<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: true, mobileMenuOpen: false }" x-init="if (localStorage.theme === 'light') { darkMode = false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DCIEN | Software de Gestión para Gimnasios y Box de Crossfit</title>
    <meta name="description" content="La plataforma más potente y simple para gestionar tu centro deportivo. Calendario versátil, control de aforos, marcas personales y marca blanca total.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; transition: background-color 0.3s, color 0.3s; }
        .text-gradient { background: linear-gradient(to right, currentColor, #a1a1a1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block; padding-right: 40px; margin-right: -40px; }
        
        .feature-card { border: 1px solid rgba(0,0,0,0.1); background: #fff; transition: all 0.35s ease; display: flex; flex-direction: column; overflow: hidden; height: 100%; position: relative; z-index: 1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .dark .feature-card { border-color: rgba(255,255,255,0.05); background: #050505; box-shadow: none; }
        
        /* Efecto PC */
        @media (min-width: 1024px) { 
            .feature-card:hover { border-color: rgba(245, 158, 11, 0.5); transform: translateY(-5px) scale(1.25); z-index: 50; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2), 0 10px 30px -10px rgba(245,158,11,0.2); } 
            .dark .feature-card:hover { box-shadow: 0 20px 40px -10px rgba(0,0,0,0.8), 0 10px 30px -10px rgba(245,158,11,0.2); }
        }

        /* Efecto Móvil */
        @media (max-width: 1023px) {
            .feature-card.mobile-active { border-color: rgba(245, 158, 11, 0.5); transform: translateY(-2px) scale(1.03); z-index: 10; box-shadow: 0 15px 30px -10px rgba(245,158,11,0.2); }
            .feature-card.mobile-active .img-container-pc { filter: grayscale(0%) brightness(1); }
            .mobile-active .icon-expand { opacity: 1 !important; }
        }

        .img-container-pc { width: 100%; aspect-ratio: 16/9; overflow: hidden; filter: grayscale(100%) brightness(0.9); transition: all 0.35s ease; border-bottom: 1px solid rgba(0,0,0,0.05); position: relative; cursor: pointer; background-color: #f3f4f6; }
        .dark .img-container-pc { filter: grayscale(100%) brightness(0.4); border-bottom-color: rgba(255,255,255,0.05); background-color: #000; }
        .feature-card:hover .img-container-pc { filter: grayscale(0%) brightness(1); }
        .img-pc { width: 100%; height: 100%; object-fit: contain; }
        .mockup-glow { box-shadow: 0 0 80px -20px rgba(245, 158, 11, 0.15); transition: all 0.5s ease; }
        .icon-expand { opacity: 0; transition: opacity 0.3s ease; }
        .feature-card:hover .icon-expand { opacity: 1; }
    </style>
    <link rel="icon" type="image/png" href="/favicon.png?v=3">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://dciengestionbox.es/">
    <meta property="og:title" content="DCIEN | Software de Gestión para Gimnasios y Box de Crossfit">
    <meta property="og:description" content="La plataforma más potente y simple para gestionar tu centro deportivo. Calendario versátil, control de aforos, marcas personales y marca blanca total.">
    <meta property="og:image" content="https://dciengestionbox.es/images/og-share.png">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://dciengestionbox.es/">
    <meta property="twitter:title" content="DCIEN | Software de Gestión para Gimnasios y Box de Crossfit">
    <meta property="twitter:description" content="La plataforma más potente y simple para gestionar tu centro deportivo. Calendario versátil, control de aforos, marcas personales y marca blanca total.">
    <meta property="twitter:image" content="https://dciengestionbox.es/images/og-share.png">
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-black dark:text-white">

    <nav class="relative z-50 p-8 max-w-7xl mx-auto flex justify-between items-center">
        <img src="/logo-training-nav.png" alt="DCIEN" class="h-8 md:h-10 w-auto object-contain dark:invert-0 invert">

        <div class="hidden md:flex items-center gap-10">
            <a href="/app/login" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition uppercase tracking-[0.2em] text-[10px] font-bold">Alumnos</a>
            <a href="/admin/login" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition uppercase tracking-[0.2em] text-[10px] font-bold">Gestores</a>
            <a href="{{ route('contact') }}" class="bg-[#f59e0b] text-black px-6 py-3 text-[10px] font-black tracking-[0.2em] hover:bg-[#d97706] transition uppercase shadow-md dark:shadow-none">Contacto</a>
            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="text-gray-500 hover:text-[#f59e0b] dark:text-gray-400 dark:hover:text-[#f59e0b] focus:outline-none transition">
                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </button>
        </div>

        <div class="flex items-center gap-4 md:hidden">
            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="text-gray-500 hover:text-[#f59e0b] dark:text-gray-400 focus:outline-none">
                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </button>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-900 dark:text-white focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                <svg x-show="mobileMenuOpen" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute top-24 left-8 right-8 bg-white dark:bg-[#050505] border border-gray-100 dark:border-white/10 p-8 flex flex-col gap-6 text-center md:hidden shadow-2xl rounded-xl dark:rounded-none z-50" style="display: none;" @click.away="mobileMenuOpen = false">
            <a href="/app/login" class="block py-2 text-gray-900 dark:text-white text-[12px] font-bold tracking-[0.3em] uppercase">Alumnos</a>
            <a href="/admin/login" class="block py-2 text-gray-900 dark:text-white text-[12px] font-bold tracking-[0.3em] uppercase border-b border-gray-100 dark:border-white/5 pb-4">Gestores</a>
            <a href="{{ route('contact') }}" class="block bg-[#f59e0b] text-black py-4 text-[12px] font-black tracking-[0.3em] uppercase rounded-lg dark:rounded-none">Contacto</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 py-12 lg:py-32 flex flex-col lg:flex-row items-center">
        <div class="lg:w-1/2 text-center lg:text-left">
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter mb-6 italic uppercase leading-[0.95] text-gray-900 dark:text-white">
                Software de Gestión para <br><span class="text-gradient">Gimnasios</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-lg mb-10 font-light leading-relaxed mx-auto lg:mx-0">
                El sistema de gestión deportiva más versátil para centros de alto rendimiento. Controla reservas, aforos y pagos locales sin intermediarios.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                <a href="/admin/register" class="bg-[#f59e0b] text-black px-12 py-5 font-bold tracking-widest uppercase hover:bg-[#d97706] transition text-sm shadow-lg dark:shadow-none">Crear mi Centro</a>
                <a href="#funciones-gestor" class="border border-gray-300 dark:border-white/20 text-gray-700 dark:text-white px-8 py-5 font-bold tracking-widest uppercase hover:bg-gray-100 dark:hover:bg-white/5 transition text-sm">Funciones</a>
                <a href="#precios" class="border border-gray-300 dark:border-white/20 text-gray-700 dark:text-white px-8 py-5 font-bold tracking-widest uppercase hover:bg-gray-100 dark:hover:bg-white/5 transition text-sm">Precios</a>
            </div>
        </div>
        <div class="lg:w-1/2 mt-20 lg:mt-0 opacity-10 dark:opacity-30 flex justify-center">
            <img src="/logo-training.png" alt="Logo DCIEN" class="w-64 md:w-96 lg:w-[25rem] object-contain select-none hover:scale-105 transition-transform duration-500 dark:invert-0 invert">
        </div>
    </main>

    <section id="funciones-gestor" class="py-32 border-t border-gray-200 dark:border-white/5 bg-gray-100 dark:bg-[#020202]">
        <div class="max-w-7xl mx-auto px-8">
            <h2 class="text-sm tracking-[0.4em] text-[#f59e0b] uppercase mb-4">Panel de Administración</h2>
            <h3 class="text-4xl font-black italic uppercase tracking-tighter mb-16 text-gray-900 dark:text-white">Sistema de Gestión Integral</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                @php
                    $adminFeatures = [
                        ["video" => "calendario-admin.mp4", "title" => "Calendario Versátil", "desc" => "Configuración anual con modificación de días específicos."],
                        ["video" => "aforos-admin.mp4", "title" => "Control de Aforos", "desc" => "Cambio de capacidad en clases concretas al instante."],
                        ["video" => "renovacion-admin.mp4", "title" => "Renovación de Bonos", "desc" => "Listado de solicitudes con confirmación de pago manual."],
                        ["video" => "marca-admin.mp4", "title" => "Marca Blanca", "desc" => "Personalización total de logo, favicon y color acento."],
                        ["video" => "disciplinas-admin.mp4", "title" => "Disciplinas", "desc" => "Crea y gestiona tus categorías de entrenamiento sin límites."],
                        ["video" => "bonos-admin.mp4", "title" => "Bonos y Tarifas", "desc" => "Configura pases, mensualidades y sesiones con control de caducidad."],
                        ["video" => "usuarios-admin.mp4", "title" => "Gestión de Usuarios", "desc" => "Administración centralizada de atletas y suscripciones."],
                        ["video" => "auditoria-admin.mp4", "title" => "Auditoría de Reservas", "desc" => "Registro detallado de asistencias, cancelaciones y penalizaciones."],
                    ];
                @endphp

                @foreach($adminFeatures as $feature)
                <div class="feature-card">
                    <div class="img-container-pc group" ondblclick="openFullscreen(this)">
                        <video src="/images/{{ $feature['video'] }}" loop muted playsinline class="img-pc"></video>
                        <div class="icon-expand absolute top-4 right-4 bg-black/60 p-2 rounded-lg cursor-pointer z-20" onclick="event.stopPropagation(); openFullscreen(this.parentElement)">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        </div>
                    </div>
                    <div class="p-8">
                        <h4 class="font-bold uppercase mb-2 tracking-widest text-sm text-gray-900 dark:text-white">{{ $feature['title'] }}</h4>
                        <p class="text-gray-500 text-xs leading-relaxed uppercase">{{ $feature['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="experiencia-atleta" class="py-32 bg-gray-50 dark:bg-[#050505] border-y border-gray-200 dark:border-white/10 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-8 relative z-10">
            <div class="text-center mb-24">
                <h2 class="text-sm tracking-[0.4em] text-[#f59e0b] uppercase mb-4">La WebApp del Alumno</h2>
                <h3 class="text-4xl lg:text-5xl font-black italic uppercase tracking-tighter text-gray-900 dark:text-white">Entrena más, teclea menos.</h3>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
                <div class="lg:w-1/2 order-2 lg:order-1 flex justify-center">
                    <div class="mockup-glow w-[280px] h-[580px] bg-white dark:bg-[#111] border-[8px] border-gray-200 dark:border-[#222] rounded-[2rem] overflow-hidden relative shadow-2xl dark:shadow-none">
                        <video src="/images/reserva.mp4" autoplay loop muted playsinline class="w-full h-full object-cover opacity-90 dark:opacity-80"></video>
                    </div>
                </div>
                <div class="lg:w-1/2 order-1 lg:order-2 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#f59e0b]/10 text-[#f59e0b] mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-3xl font-black uppercase tracking-tighter mb-4 italic text-gray-900 dark:text-white">Reserva en 1 clic</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-light mb-6">Abre la app, comprueba las plazas libres y asegura la tuya con un solo toque. Privacidad total sin listas de nombres públicas. Y si la clase está llena, la lista de espera inteligente te avisará en cuanto quede un hueco libre.</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#f59e0b]/10 text-[#f59e0b] mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <h4 class="text-3xl font-black uppercase tracking-tighter mb-4 italic text-gray-900 dark:text-white">Renueva sin fricción</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-light mb-6">Se acabaron las sorpresas en recepción. La app te avisa cuando te quedan pocas clases o días para caducar. Solicita tu nuevo bono con un botón y el gestor te lo activará en segundos.</p>
                </div>
                <div class="lg:w-1/2 flex justify-center">
                    <div class="mockup-glow w-[280px] h-[580px] bg-white dark:bg-[#111] border-[8px] border-gray-200 dark:border-[#222] rounded-[2rem] overflow-hidden relative shadow-2xl dark:shadow-none">
                        <video src="/images/renovacion.mp4" autoplay loop muted playsinline class="w-full h-full object-cover opacity-90 dark:opacity-80"></video>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
                <div class="lg:w-1/2 order-2 lg:order-1 flex justify-center">
                    <div class="mockup-glow w-[280px] h-[580px] bg-white dark:bg-[#111] border-[8px] border-gray-200 dark:border-[#222] rounded-[2rem] overflow-hidden relative shadow-2xl dark:shadow-none">
                        <video src="/images/horario.mp4" autoplay loop muted playsinline class="w-full h-full object-cover opacity-90 dark:opacity-80"></video>
                    </div>
                </div>
                <div class="lg:w-1/2 order-1 lg:order-2 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#f59e0b]/10 text-[#f59e0b] mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-3xl font-black uppercase tracking-tighter mb-4 italic text-gray-900 dark:text-white">Planifica tu semana</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-light mb-6">Consulta los horarios de los próximos 7 días de un vistazo. ¿El coach ha publicado el entrenamiento? Visualiza el WOD antes de ir al box, organiza tus días de descanso y llega mentalizado para darlo todo.</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#f59e0b]/10 text-[#f59e0b] mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <h4 class="text-3xl font-black uppercase tracking-tighter mb-4 italic text-gray-900 dark:text-white">Benchmarks Históricos</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-light mb-6">Mídete contra los WODs más icónicos. Registra tus tiempos en Fran, Murph o Cindy y visualiza tu progreso histórico con gráficas de evolución. Superar tus marcas nunca fue tan visual.</p>
                </div>
                <div class="lg:w-1/2 flex justify-center">
                    <div class="mockup-glow w-[280px] h-[580px] bg-white dark:bg-[#111] border-[8px] border-gray-200 dark:border-[#222] rounded-[2rem] overflow-hidden relative shadow-2xl dark:shadow-none">
                        <video src="/images/benchmarks.mp4" autoplay loop muted playsinline class="w-full h-full object-cover opacity-90 dark:opacity-80"></video>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
                <div class="lg:w-1/2 order-2 lg:order-1 flex justify-center">
                    <div class="mockup-glow w-[280px] h-[580px] bg-white dark:bg-[#111] border-[8px] border-gray-200 dark:border-[#222] rounded-[2rem] overflow-hidden relative shadow-2xl dark:shadow-none">
                        <video src="/images/marcas.mp4" autoplay loop muted playsinline class="w-full h-full object-cover opacity-90 dark:opacity-80"></video>
                    </div>
                </div>
                <div class="lg:w-1/2 order-1 lg:order-2 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#f59e0b]/10 text-[#f59e0b] mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h4 class="text-3xl font-black uppercase tracking-tighter mb-4 italic text-gray-900 dark:text-white">Mis Marcas Personales</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-light mb-6">Tu diario de fuerza digital. Registra tus RMs de Clean, Snatch o Back Squat. La app calculará automáticamente tus porcentajes de trabajo para que sepas exactamente cuánto cargar en cada sesión.</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-16 mb-16">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#f59e0b]/10 text-[#f59e0b] mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-3xl font-black uppercase tracking-tighter mb-4 italic text-gray-900 dark:text-white">Temporizador WOD</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-light mb-6">Un reloj profesional en tu bolsillo. Modos AMRAP, EMOM, Tabata y For Time con señales acústicas. Ideal para tus sesiones de Open Box o cuando entrenas fuera del box.</p>
                </div>
                <div class="lg:w-1/2 flex justify-center">
                    <div class="mockup-glow w-[280px] h-[580px] bg-white dark:bg-[#111] border-[8px] border-gray-200 dark:border-[#222] rounded-[2rem] overflow-hidden relative shadow-2xl dark:shadow-none">
                        <video src="/images/timer.mp4" autoplay loop muted playsinline class="w-full h-full object-cover opacity-90 dark:opacity-80"></video>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#000]">
        <div class="max-w-7xl mx-auto px-8 text-center">
            <h3 class="text-2xl font-black uppercase tracking-widest mb-16 italic text-gray-400 dark:text-gray-500">Comenzar es así de simple</h3>
            <div class="flex flex-col md:flex-row justify-center items-center gap-12 md:gap-24">
                <div class="flex flex-col items-center max-w-xs">
                    <div class="text-[#f59e0b] text-5xl font-black italic opacity-80 dark:opacity-50 mb-4">1</div>
                    <h4 class="font-bold uppercase tracking-widest mb-2 text-sm text-gray-900 dark:text-white">Escanea</h4>
                    <p class="text-xs text-gray-500 uppercase">Sin descargas molestas de tiendas. Escanea el QR en tu box y guárdalo en tu inicio.</p>
                </div>
                <div class="hidden md:block w-px h-16 bg-gray-200 dark:bg-white/10"></div>
                <div class="flex flex-col items-center max-w-xs">
                    <div class="text-[#f59e0b] text-5xl font-black italic opacity-80 dark:opacity-50 mb-4">2</div>
                    <h4 class="font-bold uppercase tracking-widest mb-2 text-sm text-gray-900 dark:text-white">Accede</h4>
                    <p class="text-xs text-gray-500 uppercase">Inicia sesión con el email que le diste al gestor en recepción.</p>
                </div>
                <div class="hidden md:block w-px h-16 bg-gray-200 dark:bg-white/10"></div>
                <div class="flex flex-col items-center max-w-xs">
                    <div class="text-[#f59e0b] text-5xl font-black italic opacity-80 dark:opacity-50 mb-4">3</div>
                    <h4 class="font-bold uppercase tracking-widest mb-2 text-sm text-gray-900 dark:text-white">Entrena</h4>
                    <p class="text-xs text-gray-500 uppercase">Reserva tu plaza, controla tu suscripción y bate tus récords.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="precios" class="py-32 bg-gray-50 dark:bg-black">
        <div class="max-w-4xl mx-auto px-8 text-center">
            <h2 class="text-4xl font-black italic uppercase tracking-tighter mb-16 text-gray-900 dark:text-white">Tarifa de Gestión</h2>
            <div class="border border-gray-200 dark:border-white/10 p-12 bg-white dark:bg-black shadow-xl dark:shadow-none rounded-xl dark:rounded-none relative overflow-hidden">
                
                <div class="text-[10px] uppercase tracking-[0.5em] text-[#f59e0b] mb-2 font-bold mt-4">Suscripción WebApp</div>
                <div class="text-6xl font-black mb-2 italic tracking-tighter text-gray-900 dark:text-white">10€<span class="text-sm font-normal text-gray-500 dark:text-gray-600">/mes</span></div>
                <div class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-8">Primer mes de prueba gratuito</div>
                
                <p class="text-gray-500 text-[10px] uppercase tracking-[0.3em] mb-12 italic leading-relaxed max-w-sm mx-auto">
                    10€/mes durante el primer año. A partir del mes 13, la cuota pasa a 50€/mes. Gestión total sin comisiones.
                </p>
                <div class="flex flex-col items-center gap-8">
                    <a href="/admin/register" class="inline-block w-full sm:w-auto bg-[#f59e0b] text-black px-12 py-5 font-bold tracking-widest uppercase hover:bg-[#d97706] transition text-xs rounded-lg dark:rounded-none shadow-md">Comenzar Mes de Prueba</a>
                    <a href="{{ route('contact') }}" class="text-[10px] tracking-[0.4em] uppercase text-gray-500 hover:text-[#f59e0b] dark:text-gray-600 dark:hover:text-[#f59e0b] transition italic border-b border-gray-200 dark:border-white/5 pb-1">¿Tienes dudas? Habla con nosotros</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="p-20 text-center text-gray-500 dark:text-gray-700 text-[10px] tracking-[0.3em] uppercase border-t border-gray-200 dark:border-white/5 bg-white dark:bg-black">
        <div class="mb-4 text-gray-600 dark:text-gray-500">&copy; 2026 DCIEN | UNIQUE & EXCLUSIVE</div>
        <div class="flex flex-wrap justify-center gap-6 opacity-80 dark:opacity-50 hover:opacity-100 transition-opacity">
            <a href="{{ route('contact') }}" class="hover:text-gray-900 dark:hover:text-white transition">Contacto</a>
            <a href="{{ route('legal.terms') }}" class="hover:text-gray-900 dark:hover:text-white transition">Aviso Legal</a>
            <a href="{{ route('legal.privacy') }}" class="hover:text-gray-900 dark:hover:text-white transition">Privacidad</a>
            <a href="{{ route('legal.cookies') }}" class="hover:text-gray-900 dark:hover:text-white transition">Cookies</a>
        </div>
    </footer>
    
    <x-cookie-banner />

    <script>
        function openFullscreen(elem) {
            const video = elem.querySelector("video");
            if (video.requestFullscreen) video.requestFullscreen();
            else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
        }

        const cards = document.querySelectorAll(".feature-card");

        // LÓGICA PC
        cards.forEach(card => {
            const video = card.querySelector("video");
            if (video) {
                card.addEventListener("mouseenter", () => video.play());
                card.addEventListener("mouseleave", () => {
                    if (!document.fullscreenElement) video.pause();
                });
            }
        });

        // LÓGICA MÓVIL (IntersectionObserver)
        if (window.innerWidth < 1024) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const video = entry.target.querySelector("video");
                    if (entry.isIntersecting) {
                        entry.target.classList.add('mobile-active');
                        if (video) video.play();
                    } else {
                        entry.target.classList.remove('mobile-active');
                        if (video && !document.fullscreenElement) video.pause();
                    }
                });
            }, {
                threshold: 0.6
            });

            cards.forEach(card => observer.observe(card));
        }
    </script>
</body>
</html>
