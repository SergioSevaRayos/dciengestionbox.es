<div id="cookie-banner" style="position: fixed; bottom: 0; left: 0; width: 100%; background-color: #111827; color: #e5e7eb; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; z-index: 9999; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 14px; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1); border-top: 1px solid #374151; flex-wrap: wrap; gap: 15px;">
    <p style="margin: 0; flex: 1; min-width: 280px; line-height: 1.5;">
        🍪 Utilizamos cookies propias y de terceros para fines analíticos y para mostrarte publicidad personalizada. 
        <a href="{{ route('legal.cookies') }}" style="color: #f59e0b; text-decoration: underline; margin-left: 5px; font-weight: 500;">Leer política</a>
    </p>
    <div style="display: flex; gap: 10px; flex-shrink: 0;">
        <button onclick="acceptCookies('rechazadas')" style="padding: 8px 16px; border: 1px solid #4b5563; border-radius: 6px; cursor: pointer; background: transparent; color: #9ca3af; font-weight: 600;">Solo necesarias</button>
        <button onclick="acceptCookies('aceptadas')" style="padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; background: #f59e0b; color: #111827; font-weight: bold;">Aceptar todas</button>
    </div>
</div>

<script>
    // Si ya hay respuesta guardada, lo ocultamos inmediatamente
    if (localStorage.getItem('dcien_cookies_consent')) {
        document.getElementById('cookie-banner').style.display = 'none';
    }

    function acceptCookies(status) {
        localStorage.setItem('dcien_cookies_consent', status);
        document.getElementById('cookie-banner').style.display = 'none';
    }
</script>
