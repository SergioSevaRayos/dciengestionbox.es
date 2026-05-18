<?php
namespace App\Livewire;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use App\Models\User;

class TopbarCredits extends Component
{
    public $credits = 0;
    public $color = 'text-gray-400';

    public function mount() { $this->actualizarDatos(); }

    #[On('credits-updated')]
    public function actualizarDatos()
    {
        if (Auth::check()) {
            $user = clone User::find(Auth::id());
            $this->credits = $user->real_credits ?? 0;
            $this->color = $this->credits > 0 ? 'text-success-600' : 'text-danger-600';
        }
    }

    public function render() { return view('livewire.topbar-credits'); }
}
