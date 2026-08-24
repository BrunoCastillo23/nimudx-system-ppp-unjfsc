<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Seeder;

class TypeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * IDs alineados con los `type_user_id` ya usados en el código:
     * - 1 Administrativo
     * - 2 Académico (UserRegistrationService::processSingleAcademicRegistration)
     * - 3 Empresa (UserRegistrationService::registerUserCompany)
     * Los nombres deben coincidir con los que compara
     * resources/js/components/app-sidebar.tsx (Administrativo/Académico/Empresa).
     */
    public function run(): void
    {
        $types = [
            1 => 'Administrativo',
            2 => 'Académico',
            3 => 'Empresa',
        ];

        foreach ($types as $id => $name) {
            TypeUser::query()->updateOrCreate(
                ['id' => $id],
                ['name' => $name, 'status' => 1]
            );
        }
    }
}
