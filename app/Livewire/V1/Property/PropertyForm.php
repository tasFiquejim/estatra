<?php

namespace App\Livewire\V1\Property;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]

#[Title('Properties')]
class PropertyForm extends Component
{
    use WithFileUploads;

    public ?Property $property = null;
    public string $property_name = '';
    public string $address = '';
    public string $description = '';
    public string $property_type = 'apartment';
    public string $status = 'active';
    public string $city = '';
    public string $state = '';
    public string $zip_code = '';
    public string $country = '';
    public $property_photo = null;

    public bool $isEdit = false;

    protected function rules()
    {
        return [
            'property_name' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'property_type' => 'required|in:apartment,house,commercial',
            'status' => ['required', Rule::enum(PropertyStatus::class)],
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'property_photo' => 'nullable|image|max:2048'
        ];
    }

    public function mount(Property $property): void
    {
        if (! $property?->exists) {
            return;
        }
        $this->property   = $property;
        $this->isEdit     = true;
        $this->fill([
            'property_name' => $property->property_name,
            'address'       => $property->address,
            'description'   => $property->description,
            'property_type' => $property->property_type ?? $this->property_type,
            'status'        => $property->status?->value ?? $this->status,
            'city'          => $property->city,
            'state'         => $property->state,
            'zip_code'      => $property->zip_code,
            'country'       => $property->country,
        ]);
    }


    public function title(): string
    {
        return $this->isEdit ? 'Edit Property' : 'Create Property';
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->property_photo) {
            if ($this->isEdit && $this->property->property_photo) {
                Storage::disk('public')->delete($this->property->property_photo);
            }
            $validated['property_photo'] = $this->property_photo->store('properties', 'public');
        }

        if ($this->isEdit) {
            $this->property->update($validated);
            $message = 'Property updated successfully!';
        } else {
            $validated['user_id'] = auth()->id();
            Property::create($validated);
            $message = 'Property created successfully!';
        }

        session()->flash('success', $message);

        return $this->redirect(route('property.index'));
    }

    public function render()
    {
        return view('livewire.v1.property.property-form');
    }
}
