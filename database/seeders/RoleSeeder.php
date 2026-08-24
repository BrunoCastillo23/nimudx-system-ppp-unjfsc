<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * IDs 1-5 corresponden exactamente a App\Enums\Role (ADMIN, SUBADMIN,
     * DTITULAR, DSUPERVISOR, ESTUDIANTE). El ID 6 (Empresa) no existe en ese
     * enum pero sí lo referencia DocumentTypeRoleSeeder::$companyRole; se
     * incluye aquí para que ese seeder no falle. Si el rol de empresa se
     * termina modelando de otra forma, revisar y ajustar ambos archivos.
     */
    public function run(): void
    {
        $roles = [
            1 => 'Administrador',
            2 => 'Subadministrador',
            3 => 'Docente Titular',
            4 => 'Docente Supervisor',
            5 => 'Estudiante',
            6 => 'Empresa',
        ];

        foreach ($roles as $id => $name) {
            Role::query()->updateOrCreate(
                ['id' => $id],
                ['name' => $name, 'status' => 1]
            );
        }
    }
}
