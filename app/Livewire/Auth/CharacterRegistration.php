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

        // Створення користувача
        $user = User::create([
            'name' => $this->nickname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Отримуємо початкові атрибути для персонажа (не обчислюємо ще max_health)
        $attributes = $this->getClassAttributes($this->class);

        // Створюємо персонажа
        $character = Character::create([
            'user_id' => $user->id,
            'nickname' => $this->nickname,
            'race' => $this->race,
            'class' => $this->class,
            'avatar' => $this->avatar,
            'base_body' => $attributes['body'],
            'body' => $attributes['body'],
            'base_strength' => $attributes['strength'],
            'strength' => $attributes['strength'],
            'base_dexterity' => $attributes['dexterity'],
            'dexterity' => $attributes['dexterity'],
            'intelligence' => $attributes['intelligence'],
            'base_intelligence' => $attributes['intelligence'],
            'base_damage' => $attributes['damage'],
            'damage' => $attributes['damage'],
            'base_hit_chance' => $attributes['hit_chance'],
            'hit_chance' => $attributes['hit_chance'],
            'base_magic_damage' => $attributes['magic_damage'],
            'magic_damage' => $attributes['magic_damage'],
            'base_magic_hit_chance' => $attributes['magic_hit_chance'],
            'magic_hit_chance' => $attributes['magic_hit_chance'],
            'base_armor' => $attributes['armor'],
            'armor' => $attributes['armor'],
            'health' => $attributes['max_health'],
            'max_health' => $attributes['max_health'],
            'base_health' => $attributes['base_health'],
            'mana' => $attributes['max_mana'],
            'max_mana' => $attributes['max_mana'],
            'base_mana' => $attributes['base_mana'],
        ]);

        // Оновлюємо атрибути персонажа після створення (наприклад, max_health)
        //$character->updateCharacterAttributes(); // викликаємо метод обчислення атрибутів

        // Логін після реєстрації
        Auth::login($user);

        session()->flash('message', 'Персонаж успішно створений!');
        return redirect()->route('livewire.world');
    }

    private function getClassAttributes($class)
    {
        return match ($class) {
            'Tank' => $this->calculateAttributes(16, 8, 8, 8),
            'Warrior' => $this->calculateAttributes(8, 16, 8, 8),
            'Assassin' => $this->calculateAttributes(8, 8, 16, 8),
            'Mage' => $this->calculateAttributes(8, 8, 8, 16),
            default => [],
        };
    }

    private function calculateAttributes($body, $strength, $dexterity, $intelligence)
    {
        // Приклад обчислення здоров'я та максимального здоров'я
        $health = $body * 8;
        $mana = $intelligence * 4;

        $damage = $strength * 1;
        $hit_chance = $dexterity * 2;

        $magic_damage = $intelligence * 1;
        $magic_hit_chance = $intelligence * 2;

        $armor = $dexterity * 3;

        // Додаємо інші атрибути, якщо потрібно:
        return [
            'base_body' => $body,
            'body' => $body,
            'base_strength' => $strength,
            'strength' => $strength,
            'base_dexterity' => $dexterity,
            'dexterity' => $dexterity,
            'intelligence' => $intelligence,
            'base_intelligence' => $intelligence,
            'base_damage' => $damage,
            'damage' => $damage,
            'base_hit_chance' => $hit_chance,
            'hit_chance' => $hit_chance,
            'base_magic_damage' => $magic_damage,
            'magic_damage' => $magic_damage,
            'base_magic_hit_chance' => $magic_hit_chance,
            'magic_hit_chance' => $magic_hit_chance,
            'base_armor' => $armor,
            'armor' => $armor,
            'health' => $health,
            'max_health' => $health,
            'base_health' => $health,
            'mana' => $mana,
            'max_mana' => $mana,
            'base_mana' => $mana,
        ];
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

