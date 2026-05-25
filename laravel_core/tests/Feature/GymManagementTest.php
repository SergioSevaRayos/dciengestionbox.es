<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Gym;
use App\Models\User;
use App\Models\Package;
use App\Models\UserPackage;
use App\Models\GymSession;
use App\Models\Booking;
use App\Models\ClassType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Filament\Facades\Filament;

class GymManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Gym $gym;
    protected User $admin;
    protected User $student;
    protected ClassType $classType;

    protected function setUp(): void
    {
        parent::setUp();

        // Configuración básica para cada test
        $this->gym = Gym::create([
            'name' => 'CrossFit Box Test',
            'slug' => 'crossfit-box-test',
            'is_subscribed' => true
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        $this->admin->gyms()->attach($this->gym->id);

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);
        $this->student->gyms()->attach($this->gym->id);

        $this->classType = ClassType::create([
            'gym_id' => $this->gym->id,
            'name' => 'WOD'
        ]);
    }

    /**
     * 1. Stripe Webhook: Bloquear y Desbloquear gimnasio
     */
    public function test_stripe_webhook_manages_gym_subscription_state(): void
    {
        // 1a. Simular pago fallido -> Bloquear
        $payloadFailed = [
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'customer' => 'cus_123',
                    'metadata' => [
                        'gym_id' => $this->gym->id
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders(['X-Test-Webhook' => 'yes'])
            ->postJson('/stripe/webhook', $payloadFailed);

        $response->assertStatus(200);
        $this->assertFalse($this->gym->fresh()->is_subscribed);

        // 1b. Simular pago exitoso -> Desbloquear
        $payloadSucceeded = [
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'customer' => 'cus_123',
                    'metadata' => [
                        'gym_id' => $this->gym->id
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders(['X-Test-Webhook' => 'yes'])
            ->postJson('/stripe/webhook', $payloadSucceeded);

        $response->assertStatus(200);
        $this->assertTrue($this->gym->fresh()->is_subscribed);
    }

    /**
     * 2. Acceso a paneles según Roles
     */
    public function test_panel_access_based_on_user_roles(): void
    {
        // Panel de App (Alumnos)
        $panelApp = \Filament\Facades\Filament::getPanel('app');
        // Panel de Admin (Gestores)
        $panelAdmin = \Filament\Facades\Filament::getPanel('admin');
        // Panel de System (SaaS)
        $panelSystem = \Filament\Facades\Filament::getPanel('system');

        // Alumno
        $this->assertTrue($this->student->canAccessPanel($panelApp));
        $this->assertFalse($this->student->canAccessPanel($panelAdmin));
        $this->assertFalse($this->student->canAccessPanel($panelSystem));

        // Admin
        $this->assertFalse($this->admin->canAccessPanel($panelApp));
        $this->assertTrue($this->admin->canAccessPanel($panelAdmin));
        $this->assertFalse($this->admin->canAccessPanel($panelSystem));

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin'
        ]);

        $this->assertFalse($superAdmin->canAccessPanel($panelApp));
        $this->assertTrue($superAdmin->canAccessPanel($panelAdmin));
        $this->assertTrue($superAdmin->canAccessPanel($panelSystem));
    }

    /**
     * 3. Consumo y devolución de créditos con Bono
     */
    public function test_booking_consumes_and_refunds_bono_credits(): void
    {
        // Asignar bono de 10 créditos válido por 30 días
        $package = Package::create([
            'gym_id' => $this->gym->id,
            'name' => 'Bono 10 Clases',
            'type' => 'bono',
            'credits' => 10,
            'price' => 50,
            'validity_days' => 30
        ]);

        $userPackage = UserPackage::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->student->id,
            'package_id' => $package->id,
            'expires_at' => now()->addDays(30)
        ]);

        $this->assertEquals(10, $userPackage->fresh()->remaining_credits);

        // Crear una clase
        $session = GymSession::create([
            'gym_id' => $this->gym->id,
            'class_type_id' => $this->classType->id,
            'start_time' => now()->addHours(2),
            'end_time' => now()->addHours(3),
            'capacity' => 15
        ]);

        // Registrar reserva
        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->student->id,
            'gym_session_id' => $session->id,
            'status' => 'booked'
        ]);

        // Verificar descuento de crédito
        $this->assertEquals(9, $userPackage->fresh()->remaining_credits);

        // Cancelar reserva y verificar devolución
        $booking->delete();
        $this->assertEquals(10, $userPackage->fresh()->remaining_credits);
    }

    /**
     * 4. Bloqueo de reserva al alcanzar límite de Tarifa
     */
    public function test_booking_respects_tariff_usage_limits(): void
    {
        // Tarifa limitada a 2 clases semanales
        $package = Package::create([
            'gym_id' => $this->gym->id,
            'name' => 'Tarifa 2/Semana',
            'type' => 'tarifa',
            'limit_type' => 'semanal',
            'limit_amount' => 2,
            'price' => 40,
            'credits' => 0,
            'validity_days' => 30
        ]);

        $userPackage = UserPackage::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->student->id,
            'package_id' => $package->id,
            'expires_at' => now()->addDays(30),
            'remaining_credits' => 0
        ]);

        // Crear 3 clases en la misma semana
        $session1 = GymSession::create([
            'gym_id' => $this->gym->id, 'class_type_id' => $this->classType->id,
            'start_time' => now()->addDays(1), 'end_time' => now()->addDays(1)->addHour(), 'capacity' => 15
        ]);
        $session2 = GymSession::create([
            'gym_id' => $this->gym->id, 'class_type_id' => $this->classType->id,
            'start_time' => now()->addDays(2), 'end_time' => now()->addDays(2)->addHour(), 'capacity' => 15
        ]);
        $session3 = GymSession::create([
            'gym_id' => $this->gym->id, 'class_type_id' => $this->classType->id,
            'start_time' => now()->addDays(3), 'end_time' => now()->addDays(3)->addHour(), 'capacity' => 15
        ]);

        // Hacer primeras dos reservas
        Booking::create(['gym_id' => $this->gym->id, 'user_id' => $this->student->id, 'gym_session_id' => $session1->id, 'status' => 'booked']);
        Booking::create(['gym_id' => $this->gym->id, 'user_id' => $this->student->id, 'gym_session_id' => $session2->id, 'status' => 'booked']);

        // La tercera reserva debe ser denegada por límite semanal alcanzado
        $thirdBooking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->student->id,
            'gym_session_id' => $session3->id,
            'status' => 'booked'
        ]);

        $this->assertFalse($thirdBooking->exists);
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->student->id,
            'gym_session_id' => $session3->id
        ]);
    }

    /**
     * 5. Validación estricta de caducidad contra la fecha de la clase
     */
    public function test_booking_is_denied_if_package_expires_before_session_starts(): void
    {
        // Pase que caduca en 2 días
        $package = Package::create([
            'gym_id' => $this->gym->id,
            'name' => 'Pase Express',
            'type' => 'bono',
            'credits' => 5,
            'price' => 20,
            'validity_days' => 2
        ]);

        UserPackage::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->student->id,
            'package_id' => $package->id,
            'expires_at' => now()->addDays(2)
        ]);

        // Clase programada para dentro de 5 días
        $session = GymSession::create([
            'gym_id' => $this->gym->id,
            'class_type_id' => $this->classType->id,
            'start_time' => now()->addDays(5),
            'end_time' => now()->addDays(5)->addHour(),
            'capacity' => 15
        ]);

        // Intentar reservar
        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->student->id,
            'gym_session_id' => $session->id,
            'status' => 'booked'
        ]);

        // Debería denegarse (el modelo no se guarda)
        $this->assertFalse($booking->exists);
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->student->id,
            'gym_session_id' => $session->id
        ]);
    }

    /**
     * 6. Lista de espera, Exención de bloqueo y Ascenso de plaza
     */
    public function test_waitlist_overflow_and_promotions(): void
    {
        \Illuminate\Support\Facades\Notification::fake();

        // Crear clase con capacidad para 1 persona
        $session = GymSession::create([
            'gym_id' => $this->gym->id,
            'class_type_id' => $this->classType->id,
            'start_time' => now()->addHours(2),
            'end_time' => now()->addHours(3),
            'capacity' => 1
        ]);

        // Alumno 1 y Alumno 2 con pases activos
        $package = Package::create([
            'gym_id' => $this->gym->id,
            'name' => 'Bono',
            'type' => 'bono',
            'credits' => 10,
            'price' => 30,
            'validity_days' => 30
        ]);

        $student2 = User::create([
            'name' => 'Student 2',
            'email' => 'student2@test.com',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);
        $student2->gyms()->attach($this->gym->id);

        UserPackage::create(['gym_id' => $this->gym->id, 'user_id' => $this->student->id, 'package_id' => $package->id, 'expires_at' => now()->addDays(10)]);
        UserPackage::create(['gym_id' => $this->gym->id, 'user_id' => $student2->id, 'package_id' => $package->id, 'expires_at' => now()->addDays(10)]);

        // Alumno 1 se apunta (plaza directa)
        $booking1 = Booking::create(['gym_id' => $this->gym->id, 'user_id' => $this->student->id, 'gym_session_id' => $session->id, 'status' => 'booked']);

        // Alumno 2 intenta apuntarse -> Entra en lista de espera
        $booking2 = Booking::create(['gym_id' => $this->gym->id, 'user_id' => $student2->id, 'gym_session_id' => $session->id, 'status' => 'waiting']);

        $this->assertEquals('booked', $booking1->fresh()->status);
        $this->assertEquals('waiting', $booking2->fresh()->status);

        // Cancelar Alumno 1 -> Promueve a Alumno 2 (notificación)
        $booking1->delete();

        // Verificar que se envió la notificación a Student 2
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $student2,
            \App\Notifications\ClassSlotAvailable::class
        );

        $this->assertNotNull($booking2->fresh()->notified_at);

        // Alumno 2 (en espera) decide desapuntarse a falta de menos de 1 hora.
        // Al estar en lista de espera (waiting), no le debe restringir la cancelación
        $this->actingAs($student2);
        
        $this->assertTrue($booking2->delete());
        $this->assertDatabaseMissing('bookings', ['id' => $booking2->id]);
    }
}
