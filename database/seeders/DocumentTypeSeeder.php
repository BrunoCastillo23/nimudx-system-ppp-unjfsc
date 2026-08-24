<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * IDs y nombres tomados de los comentarios ya existentes en
     * DocumentTypeRoleSeeder::run(). Los codes 'anexo_7' y 'anexo_8' están
     * confirmados porque SupervisionService::getSupervisionAnnexes() los usa
     * literalmente. Los codes restantes (ficha_matricula, record_academico,
     * etc.) son slugs razonables pero no se validaron contra ningún otro uso
     * hardcodeado en el código; revisa si necesitas nombres/códigos exactos.
     */
    public function run(): void
    {
        $types = [
            1 => ['name' => 'Ficha de Matrícula', 'code' => 'ficha_matricula', 'description' => 'Ficha de matrícula del semestre vigente.'],
            2 => ['name' => 'Récord Académico', 'code' => 'record_academico', 'description' => 'Récord académico del estudiante.'],
            3 => ['name' => 'FUT', 'code' => 'fut', 'description' => 'Formulario Único de Trámite.'],
            4 => ['name' => 'Carta de Presentación', 'code' => 'carta_presentacion', 'description' => 'Carta de presentación ante la empresa.'],
            5 => ['name' => 'Horario', 'code' => 'horario', 'description' => 'Horario de clases del docente titular.'],
            6 => ['name' => 'Carga Lectiva', 'code' => 'carga_lectiva', 'description' => 'Carga lectiva asignada al docente titular.'],
            7 => ['name' => 'Resolución de Designación', 'code' => 'resolucion_designacion', 'description' => 'Resolución que designa al docente titular.'],
            8 => ['name' => 'Anexo 7', 'code' => 'anexo_7', 'description' => 'Anexo 7 de supervisión de prácticas.'],
            9 => ['name' => 'Anexo 8', 'code' => 'anexo_8', 'description' => 'Anexo 8 de supervisión de prácticas.'],
        ];

        foreach ($types as $id => $data) {
            DocumentType::query()->updateOrCreate(
                ['id' => $id],
                [...$data, 'status' => 1]
            );
        }
    }
}
