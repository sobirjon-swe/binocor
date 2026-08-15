<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConstructionStagePhotoRequest;
use App\Http\Requests\StoreConstructionStageRequest;
use App\Http\Requests\UpdateConstructionStageRequest;
use App\Models\ConstructionStage;
use App\Models\ConstructionStagePhoto;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ConstructionStageController extends Controller
{
    public function index()
    {
        $stages = ConstructionStage::with('project')->latest()->paginate(15);

        return view('construction-stages.index', compact('stages'));
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get();

        return view('construction-stages.create', compact('projects'));
    }

    public function store(StoreConstructionStageRequest $request)
    {
        ConstructionStage::create($request->validated());

        return redirect()->route('construction-stages.index')->with('status', 'Qurilish bosqichi yaratildi.');
    }

    public function show(ConstructionStage $constructionStage)
    {
        $constructionStage->load('project', 'photos.user');

        return view('construction-stages.show', ['stage' => $constructionStage]);
    }

    public function edit(ConstructionStage $constructionStage)
    {
        $projects = Project::orderBy('name')->get();

        return view('construction-stages.edit', ['stage' => $constructionStage, 'projects' => $projects]);
    }

    public function update(UpdateConstructionStageRequest $request, ConstructionStage $constructionStage)
    {
        $constructionStage->update($request->validated());

        return redirect()->route('construction-stages.index')->with('status', 'Qurilish bosqichi yangilandi.');
    }

    public function destroy(ConstructionStage $constructionStage)
    {
        foreach ($constructionStage->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $constructionStage->delete();

        return redirect()->route('construction-stages.index')->with('status', 'Qurilish bosqichi o\'chirildi.');
    }

    public function storePhoto(StoreConstructionStagePhotoRequest $request, ConstructionStage $constructionStage)
    {
        $path = $request->file('photo')->store('construction-stages', 'public');

        $constructionStage->photos()->create([
            'user_id' => $request->user()->id,
            'path' => $path,
            'note' => $request->validated('note'),
        ]);

        return redirect()->route('construction-stages.show', $constructionStage)->with('status', 'Foto hisobot qo\'shildi.');
    }

    public function destroyPhoto(ConstructionStage $constructionStage, ConstructionStagePhoto $photo)
    {
        abort_unless($photo->construction_stage_id === $constructionStage->id, 404);

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('construction-stages.show', $constructionStage)->with('status', 'Foto o\'chirildi.');
    }
}
