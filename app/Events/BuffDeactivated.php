<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BuffDeactivated
{
    use SerializesModels;

    public $buffId;

    public function __construct($buffId)
    {
        $this->buffId = $buffId;
    }
}

