<?php

namespace App\Livewire\Auth;

use App\Models\Character;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CharacterRegistration extends Component
{
    public $step = 1;
    public $nickname;
    public $email;
    public $password;
    public $race;
    public $avatar;
    public $class;

    public function nextStep()
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function validateCurrentStep()
    {
        $rules = match ($this->step) {
            1 => ['nickname' => 'required|string|max:255|unique:characters,nickname', 'email' => 'required|email|unique:users,email', 'password' => 'required|min:6'],
            2 => ['race' => 'required'],
            3 => ['avatar' => 'required'],
            4 => ['class' => 'required'],
            default => [],
        };

        $this->validate($rules);
    }

    public function registerCharacter()
    {
        $this->validate([
            'nickname' => 'required|string|max:255|unique:characters,nickname',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'race' => 'required',
            'class' => 'required',
            'avatar' => 'required',
        ]);

        $user = User::create([
            'name' => $this->nickname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Character::create([
            'user_id' => $user->id,
            'nickname' => $this->nickname,
            'race' => $this->race,
            'class' => $this->class,
            'avatar' => $this->avatar,

        ]);

        Auth::login($user);

        session()->flash('message', 'Персонаж успішно створений!');
        return redirect()->route('livewire.world');
    }

    public function getAvatarsForRace()
    {
        return match ($this->race) {
            'Human' => ['human1.webp', 'human2.jpg', 'human3.jpg'],
            'Elf' => ['elf1.jpg', 'elf2.jpg', 'elf3.jpg'],
            'Orc' => ['orc1.webp', 'orc2.jpeg', 'orc3.webp'],
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.auth.character-registration');
    }
}
