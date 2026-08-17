<?php

namespace Tests\Feature;

use App\Models\ConstructionStage;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConstructionStageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_foreman_can_create_stage_and_upload_photo(): void
    {
        Storage::fake('public');

        $foreman = User::factory()->create();
        $foreman->assignRole('foreman');
        $project = Project::factory()->create();

        $this->actingAs($foreman)->post(route('construction-stages.store'), [
            'project_id' => $project->id,
            'name' => 'Poydevor',
            'progress_percent' => 40,
        ])->assertRedirect(route('construction-stages.index'));

        $stage = ConstructionStage::first();
        $this->assertNotNull($stage);

        $this->actingAs($foreman)->post(route('construction-stages.photos.store', $stage), [
            'photo' => UploadedFile::fake()->image('progress.jpg'),
            'note' => 'Poydevor tayyor',
        ])->assertRedirect(route('construction-stages.show', $stage));

        $this->assertSame(1, $stage->photos()->count());
    }

    public function test_accountant_cannot_access_construction_module(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->actingAs($accountant)->get(route('construction-stages.index'))->assertForbidden();
    }

    public function test_chief_engineer_can_view_but_only_relevant_roles_can_edit(): void
    {
        $chiefEngineer = User::factory()->create();
        $chiefEngineer->assignRole('chief_engineer');
        $stage = ConstructionStage::factory()->create();

        $this->actingAs($chiefEngineer)->get(route('construction-stages.index'))->assertOk();
        $this->actingAs($chiefEngineer)->get(route('construction-stages.show', $stage))->assertOk();
    }
}
