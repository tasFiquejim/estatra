<?php

namespace App\Livewire\Property;

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
        return view('livewire.property.property-show');
    }
}
