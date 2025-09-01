<?php

namespace App\Livewire\Property;

use Livewire\Component;
use App\Models\Property;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
#[Title('Properties')]
class PropertyForm extends Component
{
    use WithFileUploads;

    public ?Property $property = null;
    
    #[Rule('required|string|max:255')]
    public string $property_name = '';
    
    #[Rule('required|string')]
    public string $address = '';
    
    #[Rule('nullable|string')]
    public string $description = '';
    
    #[Rule('required|in:apartment,house,commercial')]
    public string $property_type = 'apartment';
    
    #[Rule('required|in:active,inactive')]
    public string $status = 'active';
    
    // Add missing location fields
    #[Rule('nullable|string|max:255')]
    public string $city = '';
    
    #[Rule('nullable|string|max:255')]
    public string $state = '';
    
    #[Rule('nullable|string|max:20')]
    public string $zip_code = '';
    
    #[Rule('nullable|string|max:255')]
    public string $country = '';
    
    // Fix field name to match model
    #[Rule('nullable|image|max:2048')]
    public $property_photo;

    public bool $isEdit = false;

    public function mount(Property $property): void
    {
        if ($property && $property->exists) {
            $this->authorize('update', $property);
            
            $this->property = $property;
            $this->isEdit = true;
            $this->property_name = $property->property_name;
            // Fix: use 'address' instead of 'location'
            $this->address = $property->address ?? '';
            $this->description = $property->description ?? '';
            $this->property_type = $property->property_type ?? 'apartment';
            $this->status = $property->status ?? 'active';
            $this->city = $property->city ?? '';
            $this->state = $property->state ?? '';
            $this->zip_code = $property->zip_code ?? '';
            $this->country = $property->country ?? '';
        }
    }

    public function title(): string
    {
        return $this->isEdit ? 'Edit Property' : 'Create Property';
    }

    public function save()
    {
        $validated = $this->validate();

        // Handle image upload with correct field name
        if ($this->property_photo) {
            $validated['property_photo'] = $this->property_photo->store('properties', 'public');
        }

        if ($this->isEdit) {
            $this->authorize('update', $this->property);
            $this->property->update($validated);
            $message = 'Property updated successfully!';
        } else {
            $validated['user_id'] = auth()->id();
            Property::create($validated);
            $message = 'Property created successfully!';
        }

        session()->flash('success', $message);
        
        return $this->redirect(route('property.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.property.property-form');
    }
}