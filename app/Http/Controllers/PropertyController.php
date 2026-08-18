<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyPhotoRequest;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Property::class);

        $properties = Property::with('project', 'primaryPhoto')->latest()->paginate(15);

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

        $property->load('project', 'contracts.customer', 'photos.user');

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

    public function storePhoto(StorePropertyPhotoRequest $request, Property $property)
    {
        $this->authorize('update', $property);

        $path = $request->file('photo')->store('properties', 'public');
        $isPrimary = $request->boolean('is_primary') || ! $property->photos()->exists();

        if ($isPrimary) {
            $property->photos()->update(['is_primary' => false]);
        }

        $property->photos()->create([
            'user_id' => $request->user()->id,
            'path' => $path,
            'is_primary' => $isPrimary,
        ]);

        return redirect()->route('properties.show', $property)->with('status', 'Rasm qo\'shildi.');
    }

    public function destroyPhoto(Property $property, PropertyPhoto $photo)
    {
        $this->authorize('update', $property);

        abort_unless($photo->property_id === $property->id, 404);

        Storage::disk('public')->delete($photo->path);
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        if ($wasPrimary) {
            $property->photos()->oldest('id')->first()?->update(['is_primary' => true]);
        }

        return redirect()->route('properties.show', $property)->with('status', 'Rasm o\'chirildi.');
    }
}
