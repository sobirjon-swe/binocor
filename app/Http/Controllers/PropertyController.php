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
        $properties = Property::with('project')->latest()->paginate(15);

        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get();

        return view('properties.create', compact('projects'));
    }

    public function store(StorePropertyRequest $request)
    {
        Property::create($request->validated());

        return redirect()->route('properties.index')->with('status', 'Obyekt yaratildi.');
    }

    public function show(Property $property)
    {
        $property->load('project', 'contracts.customer');

        return view('properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $projects = Project::orderBy('name')->get();

        return view('properties.edit', compact('property', 'projects'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $property->update($request->validated());

        return redirect()->route('properties.index')->with('status', 'Obyekt yangilandi.');
    }

    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('properties.index')->with('status', 'Obyekt o\'chirildi.');
    }
}
