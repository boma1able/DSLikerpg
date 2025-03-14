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

        $attributes = $this->getClassAttributes($this->class);

        Character::create([
            'user_id' => $user->id,
            'nickname' => $this->nickname,
            'race' => $this->race,
            'class' => $this->class,
            'avatar' => $this->avatar,
            'health' => $attributes['health'],
            'max_health' => $attributes['max_health'],
            'mana' => $attributes['mana'],
            'max_mana' => $attributes['max_mana'],
            'body' => $attributes['body'],
            'strength' => $attributes['strength'],
            'agility' => $attributes['agility'],
            'intelligence' => $attributes['intelligence'],
            'damage' => $attributes['damage'],
            'armor' => $attributes['armor'],
        ]);

        Auth::login($user);

        session()->flash('message', 'Персонаж успішно створений!');
        return redirect()->route('livewire.world');
    }

    private function getClassAttributes($class)
    {
        return match ($class) {
            'Tank' => [
            'body' => 10,
            'strength' => 4,
            'agility' => 3,
            'intelligence' => 3,
            'damage' => 3,
            'armor' => 7,
            'health' => 72,
            'max_health' => 72,
            'mana' => 10,
            'max_mana' => 10,
        ],
            'Warrior' => [
            'body' => 3,
            'strength' => 10,
            'agility' => 4,
            'intelligence' => 3,
            'damage' => 4,
            'armor' => 3,
            'health' => 38,
            'max_health' => 38,
            'mana' => 10,
            'max_mana' => 10,
        ],
            'Assassin' => [
            'body' => 3,
            'strength' => 3,
            'agility' => 10,
            'intelligence' => 4,
            'damage' => 3,
            'armor' => 5,
            'health' => 38,
            'max_health' => 38,
            'mana' => 12,
            'max_mana' => 12,
        ],
            'Mage' => [
            'body' => 4,
            'strength' => 3,
            'agility' => 3,
            'intelligence' => 10,
            'damage' => 2,
            'armor' => 3,
            'health' => 43,
            'max_health' => 43,
            'mana' => 19,
            'max_mana' => 19,
        ],
            default => [],
        };
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
