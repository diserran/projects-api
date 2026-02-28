<?php

namespace Tests\Feature;

use App\Jobs\NotifyTaskCompletedJob;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TaskFlowTest extends TestCase
{
    /**
     * Requisito: Test de creación de tarea.
     */
    public function test_can_create_a_task_successfully()
    {
        $project = Project::factory()->create();

        $response = $this->postJson(route('projects.tasks.store', $project), [
            'name' => 'Nueva Tarea Test',
            'status' => 'pending',
            'description' => 'Test description',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Nueva Tarea Test');

        $this->assertDatabaseHas('tasks', [
            'name' => 'Nueva Tarea Test',
            'project_id' => $project->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Requisito: Test de límite de tareas
     * Máximo 5 Tasks por Proyecto en estado "in_progress".
     */
    public function test_cannot_create_more_than_5_tasks_in_progress()
    {
        $project = Project::factory()->create();

        Task::factory()->count(5)->inProgress()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->postJson(route('projects.tasks.store', $project), [
            'name' => 'Tarea Sobrante',
            'status' => 'in_progress',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseCount('tasks', 5);
    }

    /**
     * Requisito: Al marcar Task como done lanzar Job.
     */
    public function test_dispatches_job_when_task_is_marked_as_done()
    {
        Queue::fake();

        $task = Task::factory()->inProgress()->create();

        $response = $this->putJson(route('tasks.update', $task), [
            'status' => 'done',
        ]);

        $response->assertStatus(200);

        Queue::assertPushed(NotifyTaskCompletedJob::class, function ($job) use ($task) {
            return $job->task->id === $task->id;
        });
    }

    /**
     * Requisito: Regla de fecha vencida.
     * Task "pending" y con due_date pasada no puede marcarse "done".
     */
    public function test_cannot_complete_a_pending_task_past_due_date()
    {
        $task = Task::factory()->overdue()->create();

        $response = $this->putJson(route('tasks.update', $task), [
            'status' => 'done',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);

        Queue::fake();
        Queue::assertNothingPushed();
    }
}
