<?php

namespace App\Services;

use App\Models\Character;
use App\Models\CharacterBuff;

class CharacterBuffService
{
    public static function updateCharacterBuffs(Character $character)
    {
        $activeBuffs = CharacterBuff::where('character_id', $character->id)
            ->where('is_active', 1)
            ->get();

        $totalBodyBonus = 0;
        $totalStrengthBonus = 0;
        $totalDexterityBonus = 0;
        $totalIntelligenceBonus = 0;
        $totalHealthBonus = 0;
        $totalManaBonus = 0;
        $totalDamageBonus = 0;
        $totalMagicDamageBonus = 0;
        $totalHitChanceBonus = 0;
        $totalMagicHitChanceBonus = 0;
        $totalDexDamageBonus = 0;
        $totalArmorBonus = 0;

        foreach ($activeBuffs as $activeBuff) {
            $buffAmount = $activeBuff->buff_amount;
            $bodyBonus = 0;
            $strengthBonus = 0;
            $dexterityBonus = 0;
            $intelligenceBonus = 0;
            $healthBonus = 0;
            $manaBonus = 0;
            $damageBonus = 0;
            $magicDamageBonus = 0;
            $hitChanceBonus = 0;
            $magicHitChanceBonus = 0;
            $dexDamageBonus = 0;
            $armorBonus = 0;

            switch ($activeBuff->buff_name) {
                case 'body_boost':
                    $bodyBonus = (int) round(($buffAmount / 100) * $character->base_body);
                    $healthBonus = $bodyBonus * 8;
                    break;
                case 'strength_boost':
                    $strengthBonus = (int) round(($buffAmount / 100) * $character->base_strength);
                    $damageBonus = $strengthBonus * 1;
                    break;
                case 'dexterity_boost':
                    $dexterityBonus = (int) round(($buffAmount / 100) * $character->base_dexterity);
                    $hitChanceBonus = $dexterityBonus * 2;
                    $dexDamageBonus = $dexterityBonus * 1;
                    $armorBonus = $dexterityBonus * 3;
                    break;
                case 'intelligence_boost':
                    $intelligenceBonus = (int) round(($buffAmount / 100) * $character->base_intelligence);
                    $magicHitChanceBonus = $intelligenceBonus * 2;
                    $magicDamageBonus = $intelligenceBonus * 1;
                    $manaBonus = $intelligenceBonus * 4;
                    break;
            }

            $totalBodyBonus += $bodyBonus;
            $totalStrengthBonus += $strengthBonus;
            $totalDexterityBonus += $dexterityBonus;
            $totalIntelligenceBonus += $intelligenceBonus;
            $totalHealthBonus += $healthBonus;
            $totalManaBonus += $manaBonus;
            $totalDamageBonus += $damageBonus;
            $totalMagicDamageBonus += $magicDamageBonus;
            $totalHitChanceBonus += $hitChanceBonus;
            $totalMagicHitChanceBonus += $magicHitChanceBonus;
            $totalDexDamageBonus += $dexDamageBonus;
            $totalArmorBonus += $armorBonus;
        }

        $character->update([
            'school_buff_body_bonus' => $totalBodyBonus,
            'school_buff_strength_bonus' => $totalStrengthBonus,
            'school_buff_dexterity_bonus' => $totalDexterityBonus,
            'school_buff_intelligence_bonus' => $totalIntelligenceBonus,
            'school_buff_health_bonus' => $totalHealthBonus,
            'school_buff_mana_bonus' => $totalManaBonus,
            'school_buff_damage_bonus' => $totalDexDamageBonus + $totalDamageBonus,
            'school_buff_magic_damage_bonus' => $totalMagicDamageBonus,
            'school_buff_hit_chance_bonus' => $totalHitChanceBonus,
            'school_buff_magic_hit_chance_bonus' => $totalMagicHitChanceBonus,
            'school_buff_armor_bonus' => $totalArmorBonus,
            'body' => $character->base_body + $totalBodyBonus + $character->school_body_bonus,
            'strength' => $character->base_strength + $totalStrengthBonus + $character->school_strength_bonus,
            'dexterity' => $character->base_dexterity + $totalDexterityBonus + $character->school_dexterity_bonus,
            'intelligence' => $character->base_intelligence + $totalIntelligenceBonus + $character->school_intelligence_bonus,
            'max_health' => $character->base_health + $totalHealthBonus + $character->school_health_bonus,
            'max_mana' => $character->base_mana + $totalManaBonus + $character->school_mana_bonus,
            'damage' => $totalDexDamageBonus + $character->base_damage + $totalDamageBonus + $character->school_damage_bonus,
            'magic_damage' => $character->base_magic_damage + $totalMagicDamageBonus + $character->school_magic_damage_bonus,
            'hit_chance' => $character->base_hit_chance + $totalHitChanceBonus + $character->school_hit_chance_bonus,
            'magic_hit_chance' => $character->base_magic_hit_chance + $totalMagicHitChanceBonus + $character->school_magic_hit_chance_bonus,
        ]);
    }
}
