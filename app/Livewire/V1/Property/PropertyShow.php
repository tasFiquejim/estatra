<?php

namespace App\Livewire\V1\Property;

use Livewire\Component;
use App\Models\Property;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PropertyShow extends Component
{
    public Property $property;

    public function mount(Property $property)
    {
        $this->property = $property->load('units');
    }
    public function render()
    {
        return view('livewire.v1.property.property-show');
    }
}
