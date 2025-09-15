<?php

namespace App\Livewire\V1\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
#[Layout('layouts.app')]

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.v1.dashboard.dashboard');
    }
}
