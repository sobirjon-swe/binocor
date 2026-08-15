<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::withCount('properties')->latest()->paginate(15);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);

        return view('projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        Project::create($request->validated());

        return redirect()->route('projects.index')->with('status', 'Loyiha yaratildi.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load('properties');

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return redirect()->route('projects.index')->with('status', 'Loyiha yangilandi.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Loyiha o\'chirildi.');
    }
}
