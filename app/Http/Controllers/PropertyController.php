<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Project;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Property::class);

        $properties = Property::with('project')->latest()->paginate(15);

        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        $this->authorize('create', Property::class);

        $projects = Project::orderBy('name')->get();

        return view('properties.create', compact('projects'));
    }

    public function store(StorePropertyRequest $request)
    {
        $this->authorize('create', Property::class);

        Property::create($request->validated());

        return redirect()->route('properties.index')->with('status', 'Obyekt yaratildi.');
    }

    public function show(Property $property)
    {
        $this->authorize('view', $property);

        $property->load('project', 'contracts.customer');

        return view('properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $this->authorize('update', $property);

        $projects = Project::orderBy('name')->get();

        return view('properties.edit', compact('property', 'projects'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $this->authorize('update', $property);

        $property->update($request->validated());

        return redirect()->route('properties.index')->with('status', 'Obyekt yangilandi.');
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        $property->delete();

        return redirect()->route('properties.index')->with('status', 'Obyekt o\'chirildi.');
    }
}
