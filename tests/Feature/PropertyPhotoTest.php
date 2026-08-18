<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_manager_can_upload_photo_and_first_upload_becomes_primary(): void
    {
        Storage::fake('public');

        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $property = Property::factory()->create();

        $this->actingAs($manager)->post(route('properties.photos.store', $property), [
            'photo' => UploadedFile::fake()->image('unit.jpg'),
        ])->assertRedirect(route('properties.show', $property));

        $photo = $property->photos()->first();
        $this->assertNotNull($photo);
        $this->assertTrue($photo->is_primary);
    }

    public function test_marking_a_new_photo_primary_demotes_the_previous_one(): void
    {
        Storage::fake('public');

        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $property = Property::factory()->create();

        $this->actingAs($manager)->post(route('properties.photos.store', $property), [
            'photo' => UploadedFile::fake()->image('one.jpg'),
        ]);
        $this->actingAs($manager)->post(route('properties.photos.store', $property), [
            'photo' => UploadedFile::fake()->image('two.jpg'),
            'is_primary' => '1',
        ]);

        $this->assertSame(1, $property->photos()->where('is_primary', true)->count());
        $this->assertSame(2, $property->photos()->count());
    }

    public function test_sales_agent_cannot_upload_property_photo(): void
    {
        Storage::fake('public');

        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');
        $property = Property::factory()->create();

        $this->actingAs($agent)->post(route('properties.photos.store', $property), [
            'photo' => UploadedFile::fake()->image('unit.jpg'),
        ])->assertForbidden();
    }

    public function test_property_index_shows_primary_photo_thumbnail(): void
    {
        Storage::fake('public');

        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $property = Property::factory()->create();
        $property->photos()->create([
            'user_id' => $manager->id,
            'path' => 'properties/thumb.jpg',
            'is_primary' => true,
        ]);

        $response = $this->actingAs($manager)->get(route('properties.index'));

        $response->assertOk();
        $response->assertSee('properties/thumb.jpg', false);
    }
}
