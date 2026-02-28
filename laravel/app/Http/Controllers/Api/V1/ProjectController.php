<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\StoreProjectRequest;
use App\Http\Requests\V1\UpdateProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Muestra todos los proyectos con paginación.
     */
    public function index()
    {
        // Ordenamos de por fecha de creación descendente
        $projects = Project::latest()->paginate(10);
        
        return ProjectResource::collection($projects);
    }

    /**
     * Crea un nuevo proyecto.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = Project::create($validated);

        return new ProjectResource($project);
    }

    /**
     * Muestra el proyecto especificado con sus tareas.
     */
    public function show(Project $project)
    {
        // Cargamos las tareas relacionadas
        $project->load('tasks');

        return new ProjectResource($project);
    }

    /**
     * Actualiza el proyecto especificado.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        $project->update($validated);

        return new ProjectResource($project);
    }

    /**
     * Elimina el proyecto especificado.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->noContent();
    }
}
