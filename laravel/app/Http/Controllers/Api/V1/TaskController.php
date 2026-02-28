<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\StoreTaskRequest;
use App\Http\Requests\V1\UpdateTaskRequest;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Resources\V1\TaskResource;
use App\Models\Project;
use App\Models\Task;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Muestra todas las tareas de un proyecto específico con paginación.
     */
    public function index(Project $project)
    {
        $tasks = $project->tasks()->paginate(10);

        return TaskResource::collection($tasks);
    }

    /**
     * Crea una nueva tarea para un proyecto específico.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        try {
            $task = $this->taskService->createTask($project, $request->validated());

            return new TaskResource($task);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la tarea',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Lista una tarea específica.
     */
    public function show(Task $task)
    {
        // Cargamos el proyecto relacionado
        $task->load('project');

        return new TaskResource($task);
    }

    /**
     * Actualiza una tarea específica.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $updatedTask = $this->taskService->updateTask($task, $request->validated());

            return new TaskResource($updatedTask);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la tarea',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Elimina una tarea específica.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
