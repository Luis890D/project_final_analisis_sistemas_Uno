<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientAccess;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuthFacade;

/**
 * Suite de Tests de Integración — Módulo Detalle de Paciente
 *
 * Verifica el comportamiento completo del API REST de pacientes,
 * incluyendo autenticación JWT, aislamiento por tenant y operaciones CRUD.
 */
#[Group('patients')]
class PatientModuleTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private Tenant $otherTenant;
    private User $user;
    private string $token;

    /**
     * Configura el entorno de prueba antes de cada test.
     * Crea un tenant, un usuario autenticado y un tenant adicional para
     * verificar el aislamiento multitenant.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Sprint 1 — Datos de prueba: tenant principal y usuario
        $this->tenant = Tenant::query()->create([
            'id'   => '00000000-0000-4000-8000-000000000001',
            'name' => 'Hospital General San Marcos (demo)',
            'slug' => 'san-marcos-demo',
            'data' => [],
        ]);

        // Tenant secundario para probar aislamiento
        $this->otherTenant = Tenant::query()->create([
            'id'   => '00000000-0000-4000-8000-000000000002',
            'name' => 'Clínica Privada Ejemplo',
            'slug' => 'clinica-ejemplo',
            'data' => [],
        ]);

        // Usuario de prueba vinculado al tenant principal
        $this->user = User::query()->create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Luis HIS',
            'email'     => 'luis@his.com',
            'password'  => bcrypt('password123'),
        ]);

        // Genera token JWT para el usuario de prueba
        $this->token = JWTAuthFacade::fromUser($this->user);
    }

    // =========================================================================
    // SPRINT 1 — Base de Datos y Modelos
    // =========================================================================

    /**
     * Sprint 1: Verifica que el modelo Patient se puede crear correctamente
     * con todos sus atributos y que la relación con el tenant funciona.
     */
    public function test_patient_model_can_be_created_with_all_fields(): void
    {
        $patient = Patient::query()->create([
            'tenant_id'  => $this->tenant->id,
            'first_name' => 'Juan',
            'last_name'  => 'Pérez Gómez',
            'dpi'        => '2534123450101',
            'birth_date' => '1985-05-15',
            'phone'      => '+502 5555-1234',
            'address'    => '5a Calle 3-45 Zona 1',
            'gender'     => 'Masculino',
            'blood_type' => 'O+',
            'allergies'  => 'Penicilina',
        ]);

        $this->assertDatabaseHas('patients', [
            'first_name' => 'Juan',
            'last_name'  => 'Pérez Gómez',
            'dpi'        => '2534123450101',
            'tenant_id'  => $this->tenant->id,
        ]);

        $this->assertEquals($this->tenant->id, $patient->tenant->id);
    }

    /**
     * Sprint 1: Verifica que el modelo PatientAccess se puede crear
     * y que la relación BelongsTo con Patient funciona correctamente.
     */
    public function test_patient_access_model_belongs_to_patient(): void
    {
        $patient = Patient::query()->create([
            'tenant_id'  => $this->tenant->id,
            'first_name' => 'María',
            'last_name'  => 'López',
            'birth_date' => '1992-10-22',
        ]);

        $access = PatientAccess::query()->create([
            'patient_id'      => $patient->id,
            'type'            => 'Consulta',
            'description'     => 'Control general. Sin novedad.',
            'access_date'     => now(),
            'doctor_in_charge' => 'Dr. Pérez',
            'status'          => 'completada',
        ]);

        $this->assertDatabaseHas('patient_accesses', [
            'patient_id' => $patient->id,
            'type'       => 'Consulta',
            'status'     => 'completada',
        ]);

        $this->assertEquals($patient->id, $access->patient->id);
    }

    /**
     * Sprint 1: Verifica que la relación hasMany de Patient → PatientAccess funciona.
     */
    public function test_patient_has_many_accesses(): void
    {
        $patient = Patient::query()->create([
            'tenant_id'  => $this->tenant->id,
            'first_name' => 'Carlos',
            'last_name'  => 'Rodríguez',
            'birth_date' => '1978-01-05',
        ]);

        PatientAccess::query()->create([
            'patient_id'      => $patient->id,
            'type'            => 'Consulta',
            'description'     => 'Primera consulta.',
            'access_date'     => now()->subDays(10),
            'doctor_in_charge' => 'Dr. Solis',
            'status'          => 'completada',
        ]);

        PatientAccess::query()->create([
            'patient_id'      => $patient->id,
            'type'            => 'Emergencia',
            'description'     => 'Dolor torácico agudo.',
            'access_date'     => now()->subDays(2),
            'doctor_in_charge' => 'Dr. Méndez',
            'status'          => 'completada',
        ]);

        $this->assertCount(2, $patient->fresh()->accesses);
    }

    // =========================================================================
    // SPRINT 2 — API REST: Listado de Pacientes (GET /api/v1/patients)
    // =========================================================================

    /**
     * Sprint 2: El usuario autenticado puede listar todos los pacientes de su tenant.
     */
    public function test_can_list_patients(): void
    {
        Patient::query()->create([
            'tenant_id'  => $this->tenant->id,
            'first_name' => 'Ana',
            'last_name'  => 'Sánchez',
            'birth_date' => '2001-07-12',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->getJson('/api/v1/patients');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['first_name' => 'Ana']);
    }

    /**
     * Sprint 2: Sin la cabecera X-Tenant-ID la API rechaza la petición con 400.
     */
    public function test_cannot_list_patients_without_tenant(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/patients');

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'La cabecera X-Tenant-ID es obligatoria.']);
    }

    /**
     * Sprint 2: Sin JWT la API rechaza la petición con 401.
     */
    public function test_cannot_list_patients_without_auth(): void
    {
        $response = $this->withHeaders([
            'X-Tenant-ID' => $this->tenant->id,
        ])->getJson('/api/v1/patients');

        $response->assertStatus(401);
    }

    // =========================================================================
    // SPRINT 2 — API REST: Detalle de Paciente (GET /api/v1/patients/{id})
    // =========================================================================

    /**
     * Sprint 2 (+ Sprint 1): El detalle de paciente retorna datos generales
     * y el historial de accesos relacionados, ordenados por fecha descendente.
     * Este es el endpoint central del módulo.
     */
    public function test_can_view_patient_detail_with_accesses(): void
    {
        $patient = Patient::query()->create([
            'tenant_id'  => $this->tenant->id,
            'first_name' => 'Juan',
            'last_name'  => 'Pérez',
            'dpi'        => '2534123450101',
            'birth_date' => '1985-05-15',
            'phone'      => '+502 5555-1234',
            'gender'     => 'Masculino',
            'blood_type' => 'O+',
            'allergies'  => 'Penicilina',
        ]);

        $olderAccess = PatientAccess::query()->create([
            'patient_id'      => $patient->id,
            'type'            => 'Consulta',
            'description'     => 'Primera visita.',
            'access_date'     => now()->subDays(10),
            'doctor_in_charge' => 'Dr. Méndez',
            'status'          => 'completada',
        ]);

        $recentAccess = PatientAccess::query()->create([
            'patient_id'      => $patient->id,
            'type'            => 'Cita',
            'description'     => 'Seguimiento.',
            'access_date'     => now()->subDays(2),
            'doctor_in_charge' => 'Dr. Méndez',
            'status'          => 'programada',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->getJson("/api/v1/patients/{$patient->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'first_name' => 'Juan',
                'last_name'  => 'Pérez',
                'blood_type' => 'O+',
                'allergies'  => 'Penicilina',
            ])
            // Verifica que el historial de accesos está incluido en la respuesta
            ->assertJsonStructure([
                'id',
                'first_name',
                'last_name',
                'dpi',
                'birth_date',
                'gender',
                'blood_type',
                'allergies',
                'accesses' => [
                    '*' => [
                        'id',
                        'type',
                        'description',
                        'access_date',
                        'doctor_in_charge',
                        'status',
                    ],
                ],
            ])
            // Verifica que hay exactamente 2 registros de acceso
            ->assertJsonCount(2, 'accesses');
    }

    /**
     * Sprint 2: Solicitar un paciente que no existe devuelve 404.
     */
    public function test_patient_detail_not_found_returns_404(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->getJson('/api/v1/patients/99999');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Paciente no encontrado o no pertenece a este tenant.']);
    }

    /**
     * Sprint 2 — Aislamiento Multitenant: Un usuario NO puede ver el expediente
     * de un paciente registrado en un tenant diferente.
     * Este es el test más crítico de seguridad del módulo.
     */
    public function test_cannot_access_patient_from_other_tenant(): void
    {
        // Paciente registrado en el OTRO tenant
        $alienPatient = Patient::query()->create([
            'tenant_id'  => $this->otherTenant->id,
            'first_name' => 'Pedro',
            'last_name'  => 'Oculto',
            'birth_date' => '1990-01-01',
        ]);

        // Usuario del tenant principal intenta acceder al paciente del otro tenant
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->getJson("/api/v1/patients/{$alienPatient->id}");

        $response->assertStatus(404);
    }

    // =========================================================================
    // SPRINT 2 — API REST: Crear Paciente (POST /api/v1/patients)
    // =========================================================================

    /**
     * Sprint 2: Un usuario autenticado puede registrar un nuevo paciente.
     */
    public function test_can_create_patient(): void
    {
        $payload = [
            'first_name' => 'Ana',
            'last_name'  => 'Sánchez Morales',
            'dpi'        => '2241987650101',
            'birth_date' => '2001-07-12',
            'phone'      => '+502 2222-3456',
            'gender'     => 'Femenino',
            'blood_type' => 'AB+',
            'allergies'  => 'Mariscos',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->postJson('/api/v1/patients', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'first_name' => 'Ana',
                'last_name'  => 'Sánchez Morales',
                'blood_type' => 'AB+',
            ]);

        $this->assertDatabaseHas('patients', [
            'dpi'       => '2241987650101',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /**
     * Sprint 2: El registro de paciente requiere los campos obligatorios.
     * Valida que el API rechaza datos incompletos con 422 Unprocessable Entity.
     */
    public function test_create_patient_requires_mandatory_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->postJson('/api/v1/patients', [
            // Falta first_name, last_name y birth_date (campos requeridos)
            'phone' => '+502 1111-1111',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'birth_date']);
    }

    // =========================================================================
    // SPRINT 2 — API REST: Actualizar Paciente (PUT /api/v1/patients/{id})
    // =========================================================================

    /**
     * Sprint 2: Un usuario autenticado puede actualizar los datos de un paciente.
     */
    public function test_can_update_patient(): void
    {
        $patient = Patient::query()->create([
            'tenant_id'  => $this->tenant->id,
            'first_name' => 'Carlos',
            'last_name'  => 'Rodríguez',
            'birth_date' => '1978-01-05',
            'phone'      => '+502 3333-0000',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->putJson("/api/v1/patients/{$patient->id}", [
            'first_name' => 'Carlos',
            'last_name'  => 'Rodríguez Ordóñez',
            'birth_date' => '1978-01-05',
            'phone'      => '+502 3333-9012',  // teléfono actualizado
            'blood_type' => 'B-',              // nuevo campo
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'last_name'  => 'Rodríguez Ordóñez',
                'phone'      => '+502 3333-9012',
                'blood_type' => 'B-',
            ]);

        $this->assertDatabaseHas('patients', [
            'id'    => $patient->id,
            'phone' => '+502 3333-9012',
        ]);
    }

    /**
     * Sprint 2: No se puede actualizar un paciente de otro tenant.
     */
    public function test_cannot_update_patient_from_other_tenant(): void
    {
        $alienPatient = Patient::query()->create([
            'tenant_id'  => $this->otherTenant->id,
            'first_name' => 'Ajeno',
            'last_name'  => 'Paciente',
            'birth_date' => '1995-01-01',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => $this->tenant->id,
        ])->putJson("/api/v1/patients/{$alienPatient->id}", [
            'first_name' => 'Modificado',
            'last_name'  => 'Ilegalmente',
            'birth_date' => '1995-01-01',
        ]);

        $response->assertStatus(404);

        // Verificar que el nombre NO fue modificado en la BD
        $this->assertDatabaseHas('patients', [
            'id'         => $alienPatient->id,
            'first_name' => 'Ajeno',
        ]);
    }

    // =========================================================================
    // SPRINT 2 — Middleware: Resolución de Tenant por Slug
    // =========================================================================

    /**
     * Sprint 2: El TenantMiddleware acepta tanto el UUID como el slug del tenant.
     * Esto permite que el usuario escriba 'san-marcos-demo' en lugar del UUID largo.
     */
    public function test_tenant_resolves_by_slug(): void
    {
        Patient::query()->create([
            'tenant_id'  => $this->tenant->id,
            'first_name' => 'Test',
            'last_name'  => 'Slug',
            'birth_date' => '1990-01-01',
        ]);

        // Usa el SLUG en lugar del UUID
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => 'san-marcos-demo',  // slug, no UUID
        ])->getJson('/api/v1/patients');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    /**
     * Sprint 2: Un tenant ID inválido (no UUID ni slug existente) devuelve 404.
     */
    public function test_invalid_tenant_id_returns_404(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-ID'   => 'tenant-que-no-existe',
        ])->getJson('/api/v1/patients');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Tenant no encontrado.']);
    }
}
