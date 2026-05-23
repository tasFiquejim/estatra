<?php

namespace Tests\Feature\Livewire;

use App\Livewire\V1\Property\PropertyForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyFormTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    public function test_it_can_upload_a_property_photo()
    {
        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('dream_house.jpg', 1000, 1000);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PropertyForm::class)
            ->set('property_name', 'Oceanview Villa')
            ->set('address', '123 Beachfront Road')
            ->set('property_type', 'house')
            ->set('status', 'active')
            ->set('property_photo', $fakeImage) // Attach our fake image!
            ->call('save');

        // 3. Assert: Verify the file WAS uploaded to our fake disk
        // Livewire automatically generates a random hash name for uploaded files
        Storage::disk('public')->assertExists('properties/' . $fakeImage->hashName());
    }
}
