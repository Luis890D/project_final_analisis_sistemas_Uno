<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientAccess;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->first() ?? Tenant::query()->create([
            'id' => '00000000-0000-4000-8000-000000000001',
            'name' => 'Hospital General San Marcos (demo)',
            'slug' => 'san-marcos-demo',
            'data' => [],
        ]);

        $patients = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Pérez Gómez',
                'dpi' => '2534123450101',
                'birth_date' => '1985-05-15',
                'phone' => '+502 5555-1234',
                'address' => '5a Calle 3-45 Zona 1, Ciudad de Guatemala',
                'gender' => 'Masculino',
                'blood_type' => 'O+',
                'allergies' => 'Penicilina, Polvo',
            ],
            [
                'first_name' => 'María',
                'last_name' => 'López Estrada',
                'dpi' => '1945876540101',
                'birth_date' => '1992-10-22',
                'phone' => '+502 4444-5678',
                'address' => 'Avenida Las Américas 12-30 Zona 14, Ciudad de Guatemala',
                'gender' => 'Femenino',
                'blood_type' => 'A+',
                'allergies' => 'Ninguna conocida',
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Rodríguez Ordóñez',
                'dpi' => '3012456780201',
                'birth_date' => '1978-01-05',
                'phone' => '+502 3333-9012',
                'address' => '7a Avenida 8-90 Zona 9, Quetzaltenango',
                'gender' => 'Masculino',
                'blood_type' => 'B-',
                'allergies' => 'Sulfa',
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Sánchez Morales',
                'dpi' => '2241987650101',
                'birth_date' => '2001-07-12',
                'phone' => '+502 2222-3456',
                'address' => 'Colonia El Maestro, Lote 12, San Marcos',
                'gender' => 'Femenino',
                'blood_type' => 'AB+',
                'allergies' => 'Mariscos',
            ],
        ];

        foreach ($patients as $index => $patientData) {
            $patientData['tenant_id'] = $tenant->id;
            $patient = Patient::query()->firstOrCreate(
                ['dpi' => $patientData['dpi']],
                $patientData
            );

            // Seed some patient access records
            if ($index == 0) {
                PatientAccess::query()->firstOrCreate([
                    'patient_id' => $patient->id,
                    'type' => 'Consulta',
                    'description' => 'Consulta general por síntomas de gripe y fiebre alta. Se receta paracetamol e hidratación.',
                    'access_date' => now()->subDays(10),
                    'doctor_in_charge' => 'Dr. Alejandro Méndez',
                    'status' => 'completada',
                ]);
                PatientAccess::query()->firstOrCreate([
                    'patient_id' => $patient->id,
                    'type' => 'Cita',
                    'description' => 'Cita de seguimiento para evaluar efectividad del tratamiento y revisión de signos vitales.',
                    'access_date' => now()->addDays(5),
                    'doctor_in_charge' => 'Dr. Alejandro Méndez',
                    'status' => 'programada',
                ]);
            } elseif ($index == 1) {
                PatientAccess::query()->firstOrCreate([
                    'patient_id' => $patient->id,
                    'type' => 'Consulta',
                    'description' => 'Control prenatal mensual. Signos vitales normales, latidos del bebé estables.',
                    'access_date' => now()->subDays(15),
                    'doctor_in_charge' => 'Dra. Patricia Arriola (Ginecóloga)',
                    'status' => 'completada',
                ]);
                PatientAccess::query()->firstOrCreate([
                    'patient_id' => $patient->id,
                    'type' => 'Emergencia',
                    'description' => 'Paciente ingresa con dolor abdominal agudo. Se realiza ecografía de urgencia. Falsa alarma de parto.',
                    'access_date' => now()->subDays(2),
                    'doctor_in_charge' => 'Dra. Patricia Arriola (Ginecóloga)',
                    'status' => 'completada',
                ]);
            } elseif ($index == 2) {
                PatientAccess::query()->firstOrCreate([
                    'patient_id' => $patient->id,
                    'type' => 'Consulta',
                    'description' => 'Evaluación de hipertensión arterial. Se ajusta dosis de Losartán.',
                    'access_date' => now()->subMonths(1),
                    'doctor_in_charge' => 'Dr. Juan Carlos Solís (Cardiólogo)',
                    'status' => 'completada',
                ]);
            } elseif ($index == 3) {
                PatientAccess::query()->firstOrCreate([
                    'patient_id' => $patient->id,
                    'type' => 'Consulta',
                    'description' => 'Limpieza dental de rutina y revisión general. Se detecta una caries leve en premolar superior.',
                    'access_date' => now()->subDays(20),
                    'doctor_in_charge' => 'Dra. Mónica Rosales (Odontóloga)',
                    'status' => 'completada',
                ]);
            }
        }
    }
}
