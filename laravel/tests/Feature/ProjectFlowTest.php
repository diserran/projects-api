<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Tests\TestCase;

class ProjectFlowTest extends TestCase
{
    /**
     * Test de Paginación: Verifica que solo devuelve 10 por página.
     */
    public function test_index_returns_paginated_projects()
    {
        Project::factory()->count(15)->create();

        $response = $this->getJson(route('projects.index'));

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'created_at'],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * Test de Creación: Sin fallos, con datos válidos.
     */
    public function test_can_create_project_successfully()
    {
        $payload = [
            'name' => 'Proyecto Apolo',
            'description' => 'Proyecto para llegar a la luna',
        ];

        $response = $this->postJson(route('projects.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Proyecto Apolo');

        $this->assertDatabaseHas('projects', $payload);
    }

    /**
     * Test de Validación: Fallo al crear sin nombre.
     */
    public function test_cannot_create_project_without_name()
    {
        $response = $this->postJson(route('projects.store'), [
            'description' => 'Lorem ipsum dolor sit amet',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test de Integridad: Borrado en Cascada.
     */
    public function test_deleting_project_deletes_associated_tasks()
    {
        $project = Project::factory()
            ->has(Task::factory()->count(3))
            ->create();

        $this->assertDatabaseCount('tasks', 3);

        $response = $this->deleteJson(route('projects.destroy', $project));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);

        $this->assertDatabaseCount('tasks', 0);
    }
}
