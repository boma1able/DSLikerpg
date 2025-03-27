<?php

namespace App\Jobs;

use App\Events\BuffDeactivated;
use App\Models\Character;
use App\Models\CharacterBuff;
use App\Services\CharacterBuffService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactivateBuffJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $buffId;

    public function __construct($buffId)
    {
        $this->buffId = $buffId;
    }

    public function handle()
    {
        $buff = CharacterBuff::find($this->buffId);

        if ($buff && $buff->is_active) {
            $buff->is_active = 0;
            $buff->save();

            $character = Character::find($buff->character_id);

            if ($character) {
                CharacterBuffService::updateCharacterBuffs($character);
            }
        }
    }


}
