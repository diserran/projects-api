<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use App\Jobs\NotifyTaskCompletedJob;
use Exception;

class TaskService
{
	public function createTask(Project $project, array $data): Task
	{
		return DB::transaction(function () use ($project, $data) {
			// Validamos la regla 2: No más de 5 tareas en progreso por proyecto
			if ($data['status'] === 'in_progress') {
				$this->validateInProgressLimit($project);
			}

			return $project->tasks()->create($data);
		});
	}

	public function updateTask(Task $task, array $data): Task
	{
		return DB::transaction(function () use ($task, $data) {
			// Validamos la regla 1: No completar tareas pendientes con fecha vencida
      if (isset($data['status']) && $data['status'] === 'done') {
        if ($task->status === 'pending' && $task->due_date < now()) {
          throw new Exception("No se puede completar una tarea pendiente con fecha vencida.");
        }
      }

      // Validamos la regla 2: No más de 5 tareas en progreso por proyecto
      if (isset($data['status']) && $data['status'] === 'in_progress' && $task->status !== 'in_progress') {
        $this->validateInProgressLimit($task->project);
      }

      $task->update($data);

      // Validamos la Regla 3: Encolar Job si se marca como "done" 
      if ($task->wasChanged('status') && $task->status === 'done') {
        NotifyTaskCompletedJob::dispatch($task);
      }

      return $task;
		});
	}

	private function validateInProgressLimit(Project $project): void
	{
		$count = $project->tasks()->where('status', 'in_progress')->count();

		if ($count >= 5) {
			throw new Exception('No se pueden tener más de 5 tareas en progreso para este proyecto.');
		}
	}
}
