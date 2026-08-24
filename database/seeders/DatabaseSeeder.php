<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Person;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Reemplaza el DatabaseSeeder por defecto de Laravel (que creaba un
     * "Test User" sin `person_id`, campo obligatorio en este proyecto y por
     * eso el seeding fallaba). Aquí se siembran los catálogos base
     * (roles, tipos de usuario, tipos de documento + su relación con roles)
     * y un usuario Administrador real, con Person + Assignment, para poder
     * iniciar sesión.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TypeUserSeeder::class,
            DocumentTypeSeeder::class,
            DocumentTypeRoleSeeder::class,
        ]);

        $semester = Semester::query()->firstOrCreate(
            ['code' => '2026-1'],
            ['cycle' => '2026-I', 'status' => 1]
        );

        $person = Person::query()->firstOrCreate(
            ['dni' => '00000000'],
            [
                'names' => 'Administrador',
                'surnames' => 'Sistema',
                'status' => 1,
            ]
        );

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@unjfsc.edu.pe'],
            [
                'person_id' => $person->id,
                'name' => 'admin',
                'password' => Hash::make('password'),
                'type_user_id' => 2, // Académico: es el que activa ACADEMIC_NAV en el sidebar
                                     // y el dashboard funcional (academic/dashboard/admin-content.tsx).
                                     // OJO: existe un bug de nombres pre-existente entre app-sidebar.tsx
                                     // (espera TypeUser 'Administrativo') y dashboard.tsx (espera 'Administrador')
                                     // para el otro camino (SuperAdmin); ninguno de los dos nombres calza con
                                     // ambos componentes a la vez, por eso se usa 'Académico' que sí está completo.
                'status' => 1,
            ]
        );

        Assignment::query()->firstOrCreate(
            [
                'user_id' => $admin->id,
                'role_id' => 1, // ADMIN
                'semester_id' => $semester->id,
            ],
            [
                'section_id' => null,
                'access_status' => 1, // FULL
                'approval_status' => 1, // APPROVED
                'review_status' => 0, // NONE
                'status' => 1, // ACTIVE
                'is_select' => true,
            ]
        );
    }
}
