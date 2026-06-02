# INSTRUCCIONES PARA GENERAR LA MEMORIA DEL PROYECTO INTEGRADO
## DCIEN GestiónBox — Software de Gestión para Gimnasios y Box de CrossFit

---

## 1. FORMATO OBLIGATORIO DEL DOCUMENTO (Word)

Aplica estas reglas SIN EXCEPCIÓN en todo el documento:

- **Fuente:** Arial
- **Tamaño:** 11 puntos
- **Alineación:** Justificado
- **Subrayado:** Nunca (salvo indicación expresa)
- **Interlineado:** 1,5
- **Tipo de página:** DIN A4
- **Márgenes:** Superior 3,5 cm · Inferior 3 cm · Izquierdo 3 cm · Derecho 2,5 cm
- **Espaciado párrafo anterior:** 6 puntos
- **Espaciado párrafo posterior:** 6 puntos

### Encabezados de página
- **Página izquierda:** `Sergio Seva Rayos` (izquierda) — `DCIEN GestiónBox` (derecha)
- **Página derecha:** Nombre del capítulo actual (centrado)

### Pies de página
- **Página izquierda:** Número de página (izquierda)
- **Página derecha:** Número de página (derecha)

### Portada
Replicar exactamente la portada del ejemplo:
- Logo EFA El Campico (arriba izquierda) + "Ciclo Formativo de Grado Superior" (arriba derecha)
- Fondo con marca de agua de estrellas (gris claro)
- **Ciclo:** `Desarrollo de Aplicaciones Web`
- **Nombre del proyecto:** `DCIEN GestiónBox`
- **Alumno:** `Sergio Seva Rayos`
- **Director de proyecto:** `[nombre del tutor]`

### Contraportada (segunda página)
Texto de autorización del centro con tabla de Calificación y Tribunal (igual que el ejemplo, con los datos del alumno y proyecto rellenados).

---

## 2. ESTRUCTURA COMPLETA DEL DOCUMENTO

Genera las siguientes secciones en este orden exacto, con numeración automática de títulos estilo IEEE 830.

---

## 3. CONTENIDO DETALLADO POR SECCIÓN

---

### FICHA DEL DOCUMENTO

| Campo | Valor |
|---|---|
| Título | Especificación de Requisitos Software — DCIEN GestiónBox |
| Alumno | Sergio Seva Rayos |
| Tutor / Director | [nombre del tutor] |
| Centro | Centro de Secundaria y Formación Profesional EFA El Campico, Jacarilla (Alicante) |
| Ciclo | CFGS Desarrollo de Aplicaciones Web |
| Revisión | 1.0 |
| Fecha | Junio 2025 |
| Estado | Entregado |

---

### ÍNDICE

Genera un índice automático con todos los títulos y sus páginas. Estructura mínima:

```
1. Introducción
   1.1 Propósito
   1.2 Alcance
   1.3 Personal involucrado
   1.4 Definiciones, acrónimos y abreviaturas
   1.5 Referencias
   1.6 Resumen
2. Descripción general
   2.1 Perspectiva del producto
   2.2 Funcionalidad del producto
   2.3 Características de los usuarios
   2.4 Restricciones
   2.5 Suposiciones y dependencias
   2.6 Evolución previsible del sistema
3. Requisitos específicos
   3.1 Requisitos comunes de los interfaces
      3.1.1 Interfaces de usuario
      3.1.2 Interfaces de hardware
      3.1.3 Interfaces de software
      3.1.4 Interfaces de comunicación
   3.2 Requisitos funcionales (RF1–RF18)
   3.3 Requisitos no funcionales
      3.3.1 Rendimiento
      3.3.2 Seguridad
      3.3.3 Fiabilidad
      3.3.4 Disponibilidad
      3.3.5 Mantenibilidad
      3.3.6 Portabilidad
4. Modelo de negocio
   4.1 Descripción del modelo
   4.2 Alternativas al modelo elegido
   4.3 Justificación del modelo elegido
5. Estudio económico del proyecto
   5.1 Recursos humanos
   5.2 Recursos materiales
   5.3 Temporalización
   5.4 Presupuesto
      5.4.1 Desarrollo
      5.4.2 Mantenimiento
6. Bibliografía
```

---

### 1. INTRODUCCIÓN

Párrafo introductorio:

> Este documento es una Especificación de Requisitos Software (ERS) para el sistema de información DCIEN GestiónBox, una plataforma SaaS de gestión integral para gimnasios y boxes de CrossFit. Esta especificación se ha estructurado basándose en las directrices del estándar IEEE Práctica Recomendada para Especificaciones de Requisitos Software ANSI/IEEE 830, 1998.

#### 1.1 Propósito

El propósito de este documento es definir las especificaciones funcionales y no funcionales del software DCIEN GestiónBox. Está dirigido al equipo de desarrollo, al tutor del proyecto y al tribunal evaluador del CFGS de Desarrollo de Aplicaciones Web del centro EFA El Campico.

La plataforma permite a propietarios de centros deportivos gestionar de forma integral sus instalaciones: reservas de clases, control de aforos, paquetes y bonos, alumnos, facturación y marca blanca personalizada. Simultáneamente ofrece a los alumnos una aplicación web progresiva (PWA) para consultar horarios, reservar clases, registrar marcas personales, participar en competiciones y temporizadores de entrenamiento.

#### 1.2 Alcance

**Nombre del producto:** DCIEN GestiónBox (`dciengestionbox.es`)

El sistema es una aplicación web SaaS (Software as a Service) multi-tenant que atiende a dos perfiles principales: el gestor del gimnasio y el alumno. La arquitectura multi-tenant garantiza el aislamiento de datos entre distintos centros deportivos sobre una infraestructura compartida.

El producto no requiere instalación: se accede desde cualquier navegador o como PWA guardada en el dispositivo móvil. El modelo de negocio se basa en una suscripción mensual por parte del gestor del gimnasio.

#### 1.3 Personal involucrado

Tabla con los siguientes datos:

| Campo | Valor |
|---|---|
| Nombre | Sergio Seva Rayos |
| Rol | Analista, diseñador y programador |
| Categoría profesional | Técnico Superior en Desarrollo de Aplicaciones Web |
| Responsabilidades | Análisis, diseño, programación, despliegue y documentación de la plataforma |
| Información de contacto | sergiosevarayos@gmail.com |
| Aprobación | [nombre del tutor] |

#### 1.4 Definiciones, acrónimos y abreviaturas

Genera una tabla con las siguientes entradas (ampliar si es necesario):

| Acrónimo / Término | Definición |
|---|---|
| SaaS | Software as a Service. Modelo de distribución de software en la nube mediante suscripción. |
| Multi-tenant | Arquitectura donde múltiples clientes (gyms) comparten la misma infraestructura con datos completamente aislados. |
| PWA | Progressive Web App. Aplicación web que puede instalarse en dispositivos móviles y funcionar como app nativa. |
| Tenant | En este sistema, cada gimnasio registrado es un tenant (inquilino) con sus propios datos y configuración. |
| PHP | Lenguaje de programación de servidor, versión 8.2+. |
| Laravel | Framework PHP versión 12 utilizado como backend principal. |
| Filament | Panel de administración para Laravel, versión 3.2. Proporciona los tres paneles de la aplicación. |
| Livewire | Framework de componentes reactivos para Laravel que permite interactividad sin JavaScript adicional. |
| Alpine.js | Framework JavaScript ligero incluido en Filament para interactividad en el frontend. |
| Tailwind CSS | Framework CSS utility-first, versión 4. Utilizado para el diseño de la interfaz. |
| Vite | Bundler de assets frontend, versión 7. |
| MySQL | Sistema de gestión de base de datos relacional usado en producción. |
| SQLite | Base de datos utilizada en entorno de desarrollo local. |
| Stripe | Pasarela de pago para gestionar las suscripciones SaaS de los gimnasios. |
| Webhook | Punto de entrada HTTP para notificaciones automáticas de Stripe. |
| ERS | Especificación de Requisitos Software. |
| RF | Requisito Funcional. |
| RNF | Requisito No Funcional. |
| WOD | Workout Of the Day. Entrenamiento del día en CrossFit. |
| AMRAP | As Many Rounds As Possible. Modalidad de entrenamiento con temporizador. |
| EMOM | Every Minute On the Minute. Modalidad de entrenamiento con temporizador. |
| Tabata | Protocolo de entrenamiento por intervalos de 20s/10s. |
| RM | Repetición Máxima. Peso máximo que un atleta puede levantar en un ejercicio. |
| Bono / Paquete | Producto que el gimnasio vende al alumno: puede ser de sesiones, mensualidad o tarifa plana. |
| Panel admin | Interfaz web en `/admin` para gestores de gimnasio. |
| Panel app | Interfaz web en `/app` para alumnos. |
| Panel system | Interfaz web en `/system` exclusiva para el super administrador de la plataforma. |
| Hostinger | Proveedor de hosting donde está desplegada la aplicación en producción. |

#### 1.5 Referencias

Tabla de referencias:

| Referencia | Título | Fecha | Autor |
|---|---|---|---|
| [IEEE830] | Standard IEEE 830-1998 — Recommended Practice for Software Requirements Specifications | 1998 | IEEE |
| [Laravel12] | Documentación oficial Laravel 12 | 2024 | Laravel LLC |
| [Filament3] | Documentación oficial Filament 3 | 2024 | Filament |
| [Stripe] | Documentación Stripe Webhooks | 2024 | Stripe Inc. |
| [Tailwind4] | Documentación Tailwind CSS v4 | 2024 | Tailwind Labs |
| [PWA] | Progressive Web Apps — web.dev | 2024 | Google |

#### 1.6 Resumen

Este documento consta de seis secciones.

La primera sección introduce el sistema, sus objetivos y el equipo involucrado.

La segunda sección describe el producto de forma general: su perspectiva, funcionalidades principales, tipos de usuario, restricciones técnicas y posibles evoluciones futuras.

La tercera sección define en detalle todos los requisitos funcionales y no funcionales que debe satisfacer el sistema, organizados según el estándar IEEE 830.

La cuarta sección expone el modelo de negocio elegido, las alternativas valoradas y su justificación.

La quinta sección presenta el estudio económico completo: recursos humanos, materiales, temporalización y presupuesto de desarrollo y mantenimiento.

La sexta sección recoge la bibliografía y referencias utilizadas durante el desarrollo del proyecto.

---

### 2. DESCRIPCIÓN GENERAL

#### 2.1 Perspectiva del producto

DCIEN GestiónBox es un producto independiente diseñado para operar en entornos web. Está pensado para accederse desde cualquier navegador moderno (Chrome, Firefox, Safari, Edge) tanto en ordenador como en dispositivo móvil, y puede instalarse como PWA en el escritorio o pantalla de inicio del teléfono.

El sistema se divide en tres paneles diferenciados según el rol del usuario:

- **Panel System (`/system`):** Exclusivo para el super administrador de la plataforma. Permite gestionar todos los gimnasios registrados, usuarios globales y suscripciones Stripe.
- **Panel Admin (`/admin/{slug-gym}`):** Para los propietarios y gestores de cada gimnasio. Acceso completo a la gestión deportiva, de usuarios, ventas y configuración de marca blanca.
- **Panel App (`/app/{slug-gym}`):** Para los alumnos del gimnasio. Interfaz simplificada orientada a la experiencia del deportista: reservas, bonos, marcas personales y arena competitiva.

El diagrama de capas es: Navegador/PWA → Hostinger (PHP 8.2 + Laravel 12) → MySQL.

#### 2.2 Funcionalidad del producto

Escribe esta sección con tres bloques diferenciados por rol:

**Super Administrador puede:**
- Ver todos los gimnasios registrados en la plataforma
- Crear, editar y eliminar gymnasios
- Gestionar usuarios de cualquier gymnasio
- Ver logs del sistema y actividad asíncrona
- Gestionar tickets de soporte de todos los centros

**Gestor del Gimnasio (admin) puede:**
- Configurar el branding del gymnasio (logo, favicon, color de acento)
- Gestionar el calendario de clases y horarios semanales
- Crear y editar tipos de clase (disciplinas)
- Controlar aforos de sesiones específicas
- Registrar y gestionar alumnos (alta, baja, paquetes)
- Vender y gestionar bonos y tarifas
- Aprobar o rechazar solicitudes de renovación de bonos
- Ver y exportar reservas y auditoría de asistencia
- Publicar WODs (entrenamiento del día)
- Gestionar competiciones Arena (equipos, WODs, resultados, leaderboard)
- Ver pagos y renovaciones
- Gestionar tickets de soporte

**Alumno (student) puede:**
- Ver el horario semanal de clases
- Reservar plaza en una sesión (con lista de espera si está llena)
- Cancelar reserva
- Ver sus bonos activos y solicitar renovación
- Registrar sus marcas personales (RM de ejercicios)
- Registrar y consultar tiempos en Benchmarks históricos (Fran, Murph, etc.)
- Participar en la Arena (competición por equipos)
- Usar el temporizador WOD (AMRAP, EMOM, Tabata, For Time)
- Editar su perfil y avatar

#### 2.3 Características de los usuarios

Genera tres tablas con el formato del ejemplo:

**Tabla: Rol Super Administrador**
| Campo | Valor |
|---|---|
| Tipo de usuario | Super Administrador |
| Formación | Técnico o desarrollador con conocimiento de la plataforma |
| Habilidades | Gestión de plataformas SaaS, administración de bases de datos |
| Actividades | Supervisión global, soporte técnico, gestión de suscripciones |

**Tabla: Rol Gestor (Admin)**
| Campo | Valor |
|---|---|
| Tipo de usuario | Propietario o encargado del gimnasio |
| Formación | Usuario con conocimientos básicos de informática |
| Habilidades | Manejo de aplicaciones web de gestión, uso habitual de smartphone |
| Actividades | Gestión diaria del centro deportivo, control de alumnos, ventas |

**Tabla: Rol Alumno (Student)**
| Campo | Valor |
|---|---|
| Tipo de usuario | Deportista inscrito en el gimnasio |
| Formación | Usuario final sin requisitos técnicos |
| Habilidades | Uso básico de smartphone y navegador web |
| Actividades | Reservar clases, consultar horarios, registrar entrenamientos |

#### 2.4 Restricciones

- Interfaz diseñada exclusivamente para uso web (no existe app nativa en tiendas).
- Acceso mediante navegador web moderno con JavaScript habilitado.
- Requiere conexión a internet activa para todas las operaciones (la PWA no funciona en modo offline).
- El sistema solo soporta un idioma: español.
- Los pagos de suscripción SaaS se procesan exclusivamente a través de Stripe.
- Lenguajes y tecnologías: PHP 8.2, Laravel 12, Filament 3.2, Livewire 3, Alpine.js, Tailwind CSS 4, Vite 7, MySQL 8.
- Desplegado en hosting compartido Hostinger con PHP y MySQL.
- Los archivos subidos (logos, avatares) se almacenan en el sistema de ficheros del servidor (Laravel Storage).

#### 2.5 Suposiciones y dependencias

- Se asume que todos los gimnasios registrados disponen de conexión a internet estable.
- El sistema depende del servicio de Stripe para procesar los pagos SaaS; si Stripe no está disponible, la activación y renovación de suscripciones queda suspendida.
- El envío de correos electrónicos (recuperación de contraseña, notificaciones) depende del servicio de correo configurado en el servidor (SMTP).
- Se asume que el hosting de Hostinger mantiene PHP 8.2+ y MySQL 8 disponibles.
- Los navegadores de los usuarios deben soportar ES6+ y Service Workers para funcionalidad PWA completa.

#### 2.6 Evolución previsible del sistema

- Integración de pasarela de pago local (Redsys / Bizum) para cobros directos entre gimnasio y alumno.
- Notificaciones push mediante Service Worker para avisos de clase completa o lista de espera.
- Módulo de estadísticas avanzadas y exportación de informes en PDF.
- Aplicación móvil nativa (iOS / Android) como extensión de la PWA actual.
- Sistema de mensajería interno entre gestor y alumnos.
- Integración con wearables para registro automático de marcas de entrenamiento.

---

### 3. REQUISITOS ESPECÍFICOS

#### 3.1 Requisitos comunes de los interfaces

##### 3.1.1 Interfaces de usuario

Describe las tres interfaces principales del sistema con capturas de pantalla o descripción textual detallada:

1. **Panel System** (`/system`): Dashboard con estadísticas globales. Sidebar con: Gimnasios, Usuarios, Tickets, Logs asíncronos.
2. **Panel Admin** (`/admin/{gym}`): Sidebar agrupado en: Gestión Deportiva (Sesiones, Calendario, Disciplinas), Entrenamiento (WOD, Arena), Gestión de Usuarios (Alumnos, Reservas), Ventas y Facturación (Bonos, Renovaciones, Pagos), Configuración (Branding, Suscripción). Widget de calendario FullCalendar y widget de reservas del día.
3. **Panel App** (`/app/{gym}`): Menú lateral con: Dashboard (resumen de bono activo y clases hoy), Horario Semanal, Arena, Benchmarks, Mis Marcas, Temporizador WOD. Perfil editable con avatar.

Incluir también los diagramas de casos de uso (uno para admin, uno para student).

##### 3.1.2 Interfaces de hardware

Los requisitos mínimos del cliente son:

| Característica | Mínimo |
|---|---|
| Dispositivo | Ordenador, tablet o smartphone |
| Conexión | Internet (mínimo 1 Mbps) |
| Navegador | Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ |
| Pantalla | 360px de ancho mínimo (responsive design) |

Los requisitos del servidor de producción (Hostinger) son:

| Característica | Valor |
|---|---|
| PHP | 8.2+ |
| MySQL | 8.0+ |
| Espacio en disco | 10 GB mínimo |
| SSL | Certificado activo (HTTPS obligatorio) |

##### 3.1.3 Interfaces de software

| Software externo | Propósito | Integración |
|---|---|---|
| Stripe | Cobro de suscripciones SaaS de los gimnasios | Webhook HTTP POST en `/stripe/webhook` + SDK stripe-php 19.4 |
| SMTP (correo) | Envío de emails de recuperación de contraseña y notificaciones | Laravel Mail con configuración SMTP |
| Tailwind CDN | CSS para la página de inicio y contacto públicas | Script CDN en `<head>` |
| Alpine.js CDN | Interactividad en páginas públicas | Script CDN en `<head>` |
| Google Fonts (Inter) | Tipografía de la landing page | Link CSS en `<head>` |

##### 3.1.4 Interfaces de comunicación

La comunicación entre el navegador y el servidor se realiza mediante:

- **Peticiones Livewire:** El frontend Filament/Livewire realiza peticiones AJAX (POST) al servidor para actualizar componentes reactivos sin recarga de página. El servidor devuelve HTML renderizado y acciones de JavaScript.
- **Webhook Stripe:** Stripe envía peticiones HTTP POST firmadas al endpoint `/stripe/webhook` cuando se producen eventos de suscripción (pago completado, suscripción cancelada, etc.). Laravel verifica la firma con la clave secreta de Stripe.
- **PWA Service Worker:** El Service Worker registrado en cada panel intercepta las peticiones de assets para cachearlos y permite la instalación de la app en el dispositivo.
- **Protocolo:** HTTPS obligatorio en producción (forzado mediante `URL::forceScheme('https')` en el `AppServiceProvider`).

---

#### 3.2 REQUISITOS FUNCIONALES

Para cada requisito, usa exactamente esta tabla (igual que el ejemplo del proyecto):

| Campo | Valor |
|---|---|
| Número de requisito | RFxx |
| Nombre de requisito | ... |
| Tipo | ☑ Requisito ☐ Restricción |
| Fuente del requisito | ... |
| Prioridad | ☑ Alta/Esencial ☐ Media/Deseado ☐ Baja/Opcional |

Seguido de la descripción del requisito en párrafo.

---

**RF1 — Registro del Gimnasio (Onboarding SaaS)**
- Tipo: Requisito
- Fuente: Modelo de negocio SaaS
- Prioridad: Alta/Esencial
- Descripción: Un nuevo propietario puede registrarse en `/admin/register` proporcionando nombre, email, contraseña y nombre del gimnasio. El sistema crea automáticamente el tenant (Gym) con su slug único y asocia al usuario con rol `admin`. El primer mes de suscripción es gratuito.

**RF2 — Autenticación de usuarios**
- Tipo: Requisito
- Fuente: Seguridad del sistema
- Prioridad: Alta/Esencial
- Descripción: El sistema dispone de tres puntos de login independientes: `/admin/login` (gestores), `/app/login` (alumnos), `/system/login` (super admin). Cada uno valida las credenciales contra la tabla `users` y comprueba que el rol del usuario corresponde con el panel solicitado. La autenticación usa sesiones Laravel con protección CSRF.

**RF3 — Recuperación de contraseña**
- Tipo: Requisito
- Fuente: Usabilidad
- Prioridad: Alta/Esencial
- Descripción: El sistema permite solicitar un enlace de recuperación de contraseña por email. El enlace redirige a través de la ruta puente `/reset-password/{token}` al formulario de restablecimiento de Filament. Válido durante 60 minutos.

**RF4 — Gestión del Calendario Semanal**
- Tipo: Requisito
- Fuente: Gestión deportiva
- Prioridad: Alta/Esencial
- Descripción: El gestor puede definir plantillas de horario semanal (`GymSchedule`) con día de la semana, hora de inicio, hora de fin, tipo de clase y capacidad máxima. El sistema usa estas plantillas para generar automáticamente las sesiones (`GymSession`) del calendario. Visualización mediante FullCalendar integrado en Filament.

**RF5 — Gestión de Sesiones Individuales**
- Tipo: Requisito
- Fuente: Gestión deportiva
- Prioridad: Alta/Esencial
- Descripción: El gestor puede crear, editar y cancelar sesiones individuales de clase. Puede modificar la capacidad máxima de una sesión concreta sin afectar al horario general. Puede publicar el WOD (entrenamiento del día) asociado a una sesión. Las sesiones canceladas notifican a los alumnos inscritos.

**RF6 — Sistema de Reservas**
- Tipo: Requisito
- Fuente: Funcionalidad core del alumno
- Prioridad: Alta/Esencial
- Descripción: El alumno puede reservar plaza en cualquier sesión disponible desde el Panel App (`/app`). Si la sesión tiene plazas libres, la reserva se confirma inmediatamente y se descuenta un crédito del bono activo del alumno (si aplica). Si la sesión está completa, el alumno puede apuntarse a la lista de espera. Al cancelarse una plaza, el primer alumno en lista de espera recibe la plaza automáticamente. El alumno puede cancelar su reserva desde la misma interfaz.

**RF7 — Gestión de Bonos y Tarifas**
- Tipo: Requisito
- Fuente: Ventas y facturación
- Prioridad: Alta/Esencial
- Descripción: El gestor puede crear paquetes de dos tipos: (a) **Bono de sesiones**: número fijo de créditos con fecha de caducidad; (b) **Tarifa plana (mensualidad)**: acceso ilimitado durante un período. El gestor asigna paquetes a alumnos y registra el pago (el cobro es externo al sistema, sin TPV integrado). El sistema descuenta créditos automáticamente al confirmar una reserva y muestra alertas cuando quedan pocas clases.

**RF8 — Solicitud de Renovación de Bono**
- Tipo: Requisito
- Fuente: Gestión de alumnos
- Prioridad: Alta/Esencial
- Descripción: El alumno puede solicitar la renovación de su bono desde el Panel App cuando este esté próximo a agotarse o caducar. La solicitud queda pendiente en el Panel Admin con estado "pendiente". El gestor la aprueba (activa el nuevo bono) o la rechaza. El alumno ve el estado de su solicitud en tiempo real.

**RF9 — Gestión de Usuarios / Alumnos**
- Tipo: Requisito
- Fuente: Gestión del gimnasio
- Prioridad: Alta/Esencial
- Descripción: El gestor puede crear, editar y dar de baja alumnos. Al crear un alumno, se le asigna un email y contraseña temporal. El gestor puede ver el historial de reservas, bonos activos y pagos de cada alumno. Puede asignar varios alumnos al mismo gimnasio (multi-gym). El super admin puede gestionar usuarios de todos los gimnasios.

**RF10 — Marcas Personales (MisMarcas)**
- Tipo: Requisito
- Fuente: Funcionalidad del alumno
- Prioridad: Media/Deseado
- Descripción: El alumno puede registrar sus Repeticiones Máximas (RM) en ejercicios personalizados (sentadilla, peso muerto, clean, snatch, etc.). El sistema calcula automáticamente los porcentajes de trabajo (50%, 60%, 70%, 80%, 90%) sobre el RM registrado. El historial de marcas se visualiza con gráfica de evolución temporal.

**RF11 — Benchmarks Históricos**
- Tipo: Requisito
- Fuente: Funcionalidad del alumno
- Prioridad: Media/Deseado
- Descripción: El alumno puede registrar sus tiempos o resultados en los WODs benchmark clásicos de CrossFit (Fran, Murph, Cindy, Grace, etc.). Cada registro incluye fecha, resultado y observaciones. El historial se visualiza con gráfica de evolución temporal para comparar el progreso a lo largo del tiempo.

**RF12 — Arena (Sistema de Competición)**
- Tipo: Requisito
- Fuente: Diferenciación del producto
- Prioridad: Media/Deseado
- Descripción: El gestor puede crear competiciones Arena con nombre, descripción, fechas y equipos participantes. Dentro de cada competición puede definir WODs de competición con su tipo de puntuación (tiempo, reps, peso). Los alumnos o el gestor registran los resultados de cada equipo en cada WOD. El sistema genera un leaderboard clasificatorio en tiempo real, visible para todos los alumnos del gimnasio.

**RF13 — Temporizador WOD**
- Tipo: Requisito
- Fuente: Funcionalidad del alumno
- Prioridad: Media/Deseado
- Descripción: El alumno dispone de un temporizador profesional en el Panel App con los siguientes modos: AMRAP (cuenta atrás con vueltas), EMOM (intervalo cada minuto con alerta sonora), Tabata (20s trabajo / 10s descanso configurable), For Time (cuenta hacia arriba hasta pulsar stop). El temporizador incluye señales acústicas en los cambios de intervalo y funciona en pantalla completa.

**RF14 — Branding y Marca Blanca**
- Tipo: Requisito
- Fuente: Propuesta de valor del producto
- Prioridad: Alta/Esencial
- Descripción: Cada gimnasio puede personalizar completamente su identidad visual: subir su propio logo (aparece en la barra de navegación del panel admin y del panel app), subir su favicon, y seleccionar un color primario de acento. El sistema aplica estos cambios dinámicamente en tiempo real mediante el middleware `ApplyGymBranding`. El manifest PWA de cada gimnasio usa su nombre y logo personalizados.

**RF15 — PWA (Progressive Web App)**
- Tipo: Requisito
- Fuente: Experiencia de usuario móvil
- Prioridad: Alta/Esencial
- Descripción: El Panel Admin y el Panel App son instalables como PWA en dispositivos iOS y Android. Cada gimnasio tiene un manifest PWA dinámico en `/manifest/{slug}.json` con su nombre, logo y colores propios. El Service Worker en `/sw.js` cachea los assets estáticos. El botón de instalación aparece automáticamente cuando el navegador detecta los criterios de instalabilidad.

**RF16 — Auditoría de Reservas (BookingLog)**
- Tipo: Requisito
- Fuente: Gestión administrativa
- Prioridad: Alta/Esencial
- Descripción: El sistema registra automáticamente cada acción sobre una reserva (creación, cancelación, penalización, entrada desde lista de espera) en la tabla `booking_logs`. El gestor puede consultar este historial filtrado por alumno, sesión o fecha desde el Panel Admin. Esto permite resolver disputas y controlar el comportamiento de los alumnos.

**RF17 — Sistema de Tickets de Soporte**
- Tipo: Requisito
- Fuente: Soporte al cliente
- Prioridad: Media/Deseado
- Descripción: Los gestores pueden crear tickets de soporte dirigidos al super administrador de la plataforma. Cada ticket tiene título, descripción, prioridad y estado (abierto, en proceso, cerrado). El super admin gestiona todos los tickets desde el Panel System. Se registra el historial de respuestas.

**RF18 — Suscripción SaaS mediante Stripe**
- Tipo: Requisito
- Fuente: Modelo de negocio
- Prioridad: Alta/Esencial
- Descripción: La plataforma cobra 10€/mes por gimnasio durante el primer año y 50€/mes a partir del mes 13. El cobro se gestiona automáticamente mediante Stripe. El webhook en `/stripe/webhook` recibe los eventos de pago, actualiza los campos `is_subscribed` y `subscription_ends_at` del gimnasio y bloquea el acceso al Panel Admin si la suscripción caduca (modo solo lectura). El primer mes es gratuito.

---

#### 3.3 REQUISITOS NO FUNCIONALES

##### 3.3.1 Requisitos de rendimiento

El sistema debe responder a las peticiones Livewire en menos de 500ms en condiciones normales de carga. Se diseñan las queries Eloquent con eager loading para evitar el problema N+1. El servidor de Hostinger debe soportar al menos 100 peticiones concurrentes. La carga inicial de la aplicación (primer acceso) no debe superar 3 segundos en conexiones 4G.

##### 3.3.2 Seguridad

- Todas las comunicaciones van cifradas mediante HTTPS (TLS 1.2+).
- Protección CSRF en todos los formularios mediante tokens de Laravel (excepto el webhook de Stripe que usa firma HMAC propia).
- Autenticación basada en sesiones con cookies `HttpOnly` y `Secure`.
- Contraseñas almacenadas con `bcrypt` mediante el cast `hashed` de Laravel.
- Middleware de tenancy garantiza que un gestor no puede acceder a datos de otro gimnasio.
- El middleware `canAccessPanel()` verifica el rol del usuario antes de permitir acceso a cada panel.
- Logs de actividad mediante Spatie Activity Log para auditoría de cambios críticos.
- El visor de logs (`/log-viewer`) está restringido exclusivamente al rol `super_admin`.
- Las claves de API de Stripe se almacenan en variables de entorno (`.env`), nunca en el código.

##### 3.3.3 Fiabilidad

El sistema garantiza su funcionamiento siempre que el hosting de Hostinger y el servicio de Stripe estén operativos. Los errores del servidor (HTTP 500) son capturados por el hook global de Livewire y mostrados al usuario como notificaciones Filament en lugar de pantallas de error técnicas. Los errores de sesión caducada (HTTP 419) y de pérdida de conexión (status 0) también son interceptados y mostrados con mensajes amigables.

##### 3.3.4 Disponibilidad

La plataforma debe estar disponible el 99,5% del tiempo. El hosting de Hostinger ofrece SLA de alta disponibilidad. Los mantenimientos planificados se comunicarán con antelación mínima de 24h. El Service Worker de la PWA permite que la interfaz ya cargada sea funcional en caso de pérdida temporal de conexión (assets cacheados).

##### 3.3.5 Mantenibilidad

- Código organizado siguiendo la estructura estándar de Laravel (MVC + Service Providers).
- Los tres paneles Filament están completamente separados en `Filament/Admin/`, `Filament/App/` y `Filament/System/`, facilitando el mantenimiento independiente.
- Las migraciones de base de datos versionadas con `artisan migrate` permiten actualizar el esquema de forma controlada.
- El archivo `.env` centraliza toda la configuración dependiente del entorno.
- Los tests unitarios e integración con PHPUnit permiten verificar la correctitud del sistema tras cambios.

##### 3.3.6 Portabilidad

La aplicación funciona en cualquier navegador moderno (Chrome, Firefox, Safari, Edge) tanto en versión escritorio como móvil. El diseño responsive con Tailwind CSS garantiza una experiencia óptima en pantallas desde 360px hasta 4K. La instalación como PWA permite acceso desde la pantalla de inicio del teléfono en iOS y Android sin necesidad de tiendas de aplicaciones.

---

### 4. MODELO DE NEGOCIO

#### 4.1 Descripción del modelo

DCIEN GestiónBox opera bajo el modelo SaaS (Software as a Service) de suscripción mensual B2B. Los clientes directos son los propietarios y gestores de gimnasios y boxes de CrossFit, quienes pagan una cuota mensual para usar la plataforma. Los alumnos del gimnasio usan la plataforma de forma gratuita como beneficio del servicio contratado por su centro.

**Estructura de precios:**
- Mes 1 a 12: **10€/mes** por gimnasio
- Mes 13 en adelante: **50€/mes** por gimnasio
- El primer mes es completamente gratuito como período de prueba

El cobro es automático mediante Stripe. El gimnasio introduce su tarjeta una sola vez y el sistema gestiona los cobros recurrentes. Si el pago falla, el acceso al panel de gestión queda en modo lectura hasta regularizar el pago.

#### 4.2 Alternativas al modelo elegido

Se valoraron las siguientes alternativas:

1. **Pago por uso / créditos:** Cobrar por número de reservas o alumnos. Descartado porque genera incertidumbre en los clientes y dificulta la planificación financiera del gimnasio.
2. **Freemium:** Versión gratuita con funciones limitadas y versión de pago completa. Descartado porque la gestión de dos niveles de funcionalidad aumenta la complejidad de mantenimiento significativamente.
3. **Licencia perpetua:** Pago único por instalación propia del software. Descartado porque elimina los ingresos recurrentes y requiere soporte técnico por instalación individual.
4. **Comisión por transacción:** Cobrar un porcentaje de cada bono vendido. Descartado porque los cobros de bonos son externos al sistema (el gimnasio cobra en efectivo o transferencia), lo que lo hace inviable.

#### 4.3 Justificación del modelo elegido

El modelo de suscripción mensual SaaS es el estándar del sector (Mindbody, Glofox, WodBuster) y tiene las siguientes ventajas clave para este proyecto:

- **Ingresos predecibles y recurrentes** que permiten planificar el desarrollo y mantenimiento.
- **Barrera de entrada baja** gracias al primer mes gratuito: el gimnasio puede probar el sistema sin riesgo.
- **Precio de introducción competitivo** (10€/mes vs. 50€+ de la competencia) para captar cuota de mercado inicial.
- **Escalabilidad:** un solo desarrollador puede gestionar múltiples clientes sin coste incremental por gimnasio.
- **Alineación de incentivos:** el desarrollador tiene incentivo de mantener y mejorar el producto continuamente para retener suscriptores.

---

### 5. ESTUDIO ECONÓMICO DEL PROYECTO

#### 5.1 Recursos humanos

El desarrollo ha sido realizado íntegramente por una sola persona: Sergio Seva Rayos, Técnico Superior en Desarrollo de Aplicaciones Web.

Estimación de horas totales: **480 horas**

Desglose:
| Actividad | Horas |
|---|---|
| Investigación y formación (Laravel, Filament, Stripe) | 60 h |
| Análisis y diseño del sistema | 30 h |
| Programación backend (modelos, controladores, migraciones, lógica) | 180 h |
| Programación frontend (Filament, vistas Blade, PWA) | 120 h |
| Pruebas y corrección de errores | 50 h |
| Despliegue y configuración en producción | 20 h |
| Documentación (esta memoria) | 20 h |
| **Total** | **480 h** |

Coste estimado (tarifa junior: 12€/h): **5.760€**

#### 5.2 Recursos materiales

| Recurso | Descripción | Coste |
|---|---|---|
| Ordenador portátil | Equipo de desarrollo principal | 800€ (amortización) |
| Hosting Hostinger | Plan Business con MySQL, PHP 8.2 y SSL | 11,99€/mes |
| Dominio | `dciengestionbox.es` (1 año) | 12€/año |
| VS Code | IDE principal (gratis) | 0€ |
| Laragon | Entorno de desarrollo local Windows (gratis) | 0€ |
| Stripe | Pasarela de pago (sin coste fijo; comisión solo en transacciones reales) | 0€ fijo |
| **Total recursos materiales (primer año)** | | **~968€** |

#### 5.3 Temporalización

El proyecto se ha desarrollado a lo largo de aproximadamente 12 semanas, compaginando estudio y desarrollo.

Incluir diagrama de Gantt con las siguientes fases:

| Semana | Actividad principal |
|---|---|
| 1-2 | Investigación del stack (Laravel 12, Filament 3), configuración del entorno |
| 3-4 | Diseño de la base de datos, modelos Eloquent, migraciones |
| 5-6 | Panel System y Panel Admin base (recursos CRUD) |
| 7-8 | Lógica de reservas, bonos, calendario FullCalendar |
| 9-10 | Panel App (alumno): horario, reservas, marcas, benchmarks, Arena, temporizador |
| 11 | Integración Stripe, PWA, branding dinámico, despliegue en Hostinger |
| 12 | Pruebas, corrección de bugs, documentación |

#### 5.4 Presupuesto

##### 5.4.1 Desarrollo

| Concepto | Coste |
|---|---|
| Recursos humanos (480h × 12€/h) | 5.760€ |
| Recursos materiales (amortización equipo) | 800€ |
| Dominio (1 año) | 12€ |
| Hosting primer año | 143,88€ |
| **Total desarrollo** | **6.715,88€** |

##### 5.4.2 Mantenimiento anual

| Concepto | Coste anual |
|---|---|
| Hosting Hostinger | 143,88€ |
| Dominio | 12€ |
| Actualizaciones y soporte (estimado 2h/mes × 12€/h × 12 meses) | 288€ |
| **Total mantenimiento anual** | **443,88€** |

**Punto de equilibrio:** Con el precio de 10€/mes por gimnasio, el sistema necesita **45 gimnasios activos** durante 12 meses para cubrir los costes de desarrollo. Con el precio de 50€/mes (a partir del mes 13), solo se necesitan **9 gimnasios** para cubrir el mantenimiento anual.

---

### 6. BIBLIOGRAFÍA

Lista en formato APA:

- Laravel LLC. (2024). *Laravel 12 Documentation*. https://laravel.com/docs/12.x
- Filament. (2024). *Filament v3 Documentation*. https://filamentphp.com/docs
- Livewire. (2024). *Livewire v3 Documentation*. https://livewire.laravel.com/docs
- Tailwind Labs. (2024). *Tailwind CSS v4 Documentation*. https://tailwindcss.com/docs
- Stripe Inc. (2024). *Stripe API Reference & Webhooks*. https://stripe.com/docs/api
- Google. (2024). *Progressive Web Apps — web.dev*. https://web.dev/progressive-web-apps
- IEEE. (1998). *IEEE Std 830-1998 — Recommended Practice for Software Requirements Specifications*. IEEE.
- Spatie. (2024). *Laravel Activity Log*. https://spatie.be/docs/laravel-activity-log
- Saade. (2024). *Filament FullCalendar Plugin*. https://github.com/saade/filament-fullcalendar
- Hostinger. (2024). *Hosting Business Plan Documentation*. https://www.hostinger.es

---

## 4. NOTAS ADICIONALES PARA LA IA QUE GENERE EL DOCUMENTO

1. **No inventar información.** Todos los datos técnicos de esta guía son reales y deben usarse exactamente como aparecen.
2. **Tablas de requisitos:** Usar exactamente el formato del ejemplo (con las casillas de checkbox simuladas con ☑ y ☐).
3. **El nombre del tutor/director** aparece como `[nombre del tutor]` en este documento porque no se ha proporcionado. Sustitúyelo cuando se conozca o deja el espacio en blanco para rellenarlo manualmente.
4. **Capturas de pantalla:** Los apartados 3.1.1 y 5.3 (Gantt) deben incluir capturas o diagramas reales. Si no se pueden generar automáticamente, insertar un marcador `[INSERTAR CAPTURA: descripción]` para rellenar manualmente.
5. **Numeración de páginas:** Comenzar la numeración en la página del índice (portada y contraportada no cuentan). La portada y contraportada no llevan encabezado ni pie.
6. **Extensión estimada:** Entre 40 y 60 páginas.
7. **El documento final** debe entregarse en formato `.docx` (Word) para permitir ajustes finales.
