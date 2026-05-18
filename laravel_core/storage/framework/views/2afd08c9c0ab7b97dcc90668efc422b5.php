
                <style>
                    /* Aniquilamos cualquier cartel oscuro nativo de offline */
                    [wire\:offline] { display: none !important; }
                    .fi-offline-indicator { display: none !important; }
                </style>
                <script>
                    document.addEventListener("livewire:init", () => {
                        
                        // Capturamos cualquier intento de petición fallida al servidor
                        Livewire.hook("request", ({ fail }) => {
                            fail(({ status, preventDefault }) => {
                                
                                // CASO 1: Sin internet al pulsar un botón (Status 0)
                                if (status === 0) {
                                    preventDefault();
                                    new FilamentNotification()
                                        .title("Acción cancelada")
                                        .body("No tienes conexión a internet. Revisa tu red y vuelve a intentarlo.")
                                        .warning()
                                        .duration(5000)
                                        .send();
                                }
                                // CASO 2: Error 500 (Fallo interno del servidor)
                                else if (status >= 500) {
                                    preventDefault();
                                    new FilamentNotification()
                                        .title("Error del Servidor")
                                        .body("Se ha producido un error técnico. El equipo ha sido notificado.")
                                        .danger()
                                        .duration(8000)
                                        .send();
                                }
                                // CASO 3: Error 419 (Token CSRF caducado por dejar la web abierta mucho tiempo)
                                else if (status === 419) {
                                    preventDefault();
                                    new FilamentNotification()
                                        .title("Sesión caducada")
                                        .body("Tu sesión ha estado inactiva. Por favor, recarga la página.")
                                        .warning()
                                        .duration(8000)
                                        .send();
                                }
                            });
                        });

                        // Notificación pasiva cuando el navegador pierde o recupera la red
                        window.addEventListener("offline", () => {
                            new FilamentNotification()
                                .title("Te has desconectado")
                                .body("Has perdido la señal de internet.")
                                .warning()
                                .send();
                        });

                        window.addEventListener("online", () => {
                            new FilamentNotification()
                                .title("Conexión Restaurada")
                                .body("Vuelves a estar en línea.")
                                .success()
                                .send();
                        });
                    });
                </script>
            <?php /**PATH C:\Users\Trending Pc\Documents\dciengestionbox.es\laravel_core\storage\framework\views/2a141752f673c9437d3e944676c00396.blade.php ENDPATH**/ ?>