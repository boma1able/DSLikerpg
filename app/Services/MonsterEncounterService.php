<?php

namespace App\Services;

class MonsterEncounterService
{
    public static function getMessage(string $name): string
    {
        return match ($name) {
            'Rat' => "Ви зустріли {$name}! Його маленькі очі блищать у темряві, а гострі зуби готові до атаки.",
            'Cat' => "Ви зустріли {$name}! Її вигнута спина та гострі кігті нагадують, що вона не така безневинна, як здається.",
            'Dog' => "Ви зустріли {$name}! Його гарчачий голос і скалений вигляд показують, що він не дасть вам пройти просто так.",
            'Beggar' => "Ви зустріли {$name}! Його брудний одяг і порожні очі приховують небезпеку, яку ви ще не усвідомлюєте.",
            'Bandit' => "Ви зустріли {$name}! Його ніж блищить у сонячному світлі, а посмішка викликає холод по спині.",
            'Thief' => "Ви зустріли {$name}! Його рухи швидкі та непомітні, але ви відчуваєте, що він уже щось задумав.",
            'Guard' => "Ви зустріли {$name}! Його сталевий погляд і спис нагадують, що він не пропустить вас без бою.",
            'Knight' => "Ви зустріли {$name}! Його обладунки сяють, а меч готовий до битви, наче він сам є легендою.",
            default => "Ви зустріли {$name}!", // Запасний варіант
        };
    }
}

