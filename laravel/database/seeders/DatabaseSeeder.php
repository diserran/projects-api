<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear proyecto con muchas tareas en progreso
        $projectBig = Project::factory()->create([
            'name' => 'Proyecto Muchas Tareas',
        ]);

        Task::factory()->count(5)->inProgress()->create([
            'project_id' => $projectBig->id,
        ]);

        // Crear proyecto con tareas vencidas
        $projectOverdue = Project::factory()->create([
            'name' => 'Proyecto Tareas Vencidas',
        ]);

        Task::factory()->count(3)->overdue()->create([
            'project_id' => $projectOverdue->id,
        ]);

        // Crear prouyectos aleatorios
        Project::factory(3)->hasTasks(4)->create();
    }
}
