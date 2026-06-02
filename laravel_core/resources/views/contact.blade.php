<!DOCTYPE html>
<html lang="es" class="bg-black text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | DCIEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 sm:p-20 font-sans leading-relaxed">
    <div class="max-w-4xl mx-auto">
        <a href="/" class="text-[10px] tracking-[0.3em] uppercase text-gray-500 hover:text-white transition inline-block mb-12">← Volver al Inicio</a>
        
        @if(session('success')) <div class="bg-green-500/10 border border-green-500 text-green-500 p-4 mb-6 text-xs uppercase tracking-widest italic font-bold">{{ session('success') }}</div> @endif<h1 class="text-4xl font-black italic uppercase tracking-tighter mb-8">Contacto</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
            <div class="text-gray-400 space-y-8 text-sm">
                <section>
                    <h2 class="text-white font-bold uppercase tracking-widest text-[10px] mb-4">Atención al cliente</h2>
                    <p>Si eres el dueño de un centro y quieres implementar **DCIEN**, o si ya eres usuario y tienes alguna duda técnica, escríbenos.</p>
                </section>

                <section>
                    <h2 class="text-white font-bold uppercase tracking-widest text-[10px] mb-4">Email directo</h2>
                    <p class="text-white font-bold tracking-wider italic text-xl">contacto@d-cien.es</p>
                </section>

                <section>
                    <h2 class="text-white font-bold uppercase tracking-widest text-[10px] mb-4">Sede Central</h2>
                    <p>Alicante, España. 🚀 TEST DEV v2</p>
                </section>
            </div>

            <div class="border border-white/10 p-8 bg-white/2">
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6"> @csrf
                    <div>
                        <label class="block text-[9px] uppercase tracking-[0.2em] text-gray-500 mb-2">Nombre completo</label>
                        <input type="text" name="name" required class="w-full bg-black border border-white/10 p-3 text-sm focus:border-white outline-none transition text-white">
                    </div>
                    <div>
                        <label class="block text-[9px] uppercase tracking-[0.2em] text-gray-500 mb-2">Email de contacto</label>
                        <input type="email" name="email" required class="w-full bg-black border border-white/10 p-3 text-sm focus:border-white outline-none transition text-white">
                    </div>
                    <div>
                        <label class="block text-[9px] uppercase tracking-[0.2em] text-gray-500 mb-2">Asunto / Mensaje</label>
                        <textarea name="message" required rows="4" class="w-full bg-black border border-white/10 p-3 text-sm focus:border-white outline-none transition text-white"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-white text-black py-4 text-[10px] font-black uppercase tracking-[0.3em] hover:bg-gray-200 transition">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
