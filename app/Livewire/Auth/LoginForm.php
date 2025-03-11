<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class LoginForm extends Component
{
    public $email;
    public $password;
    public $loggedIn = false;

    public function mount()
    {
        if (Auth::check()) {
            $this->loggedIn = true;
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {

            $this->loggedIn = true;
            return redirect()->route('livewire.world');
        }

        // Якщо дані для входу невірні
        session()->flash('error', 'Невірні дані для входу!');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}

