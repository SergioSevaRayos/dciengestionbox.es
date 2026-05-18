<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .header { background-color: #000; color: #ffffff; padding: 20px; text-align: center; }
        .content { padding: 30px; background-color: #ffffff; }
        .field { margin-bottom: 20px; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px; }
        .label { font-size: 10px; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.1em; font-weight: bold; }
        .value { font-size: 16px; color: #111827; margin-top: 5px; }
        .footer { background-color: #f9fafb; padding: 15px; text-align: center; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; text-transform: uppercase; letter-spacing: 0.3em; font-style: italic;">DCIEN</h2>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Nombre del remitente</div>
                <div class="value">{{ $data['name'] }}</div>
            </div>
            <div class="field">
                <div class="label">Email de contacto</div>
                <div class="value">{{ $data['email'] }}</div>
            </div>
            <div class="field">
                <div class="label">Mensaje</div>
                <div class="value" style="white-space: pre-wrap;">{{ $data['message'] }}</div>
            </div>
        </div>
        <div class="footer">
            Este mensaje ha sido generado automáticamente desde dciengestionbox.es
        </div>
    </div>
</body>
</html>
