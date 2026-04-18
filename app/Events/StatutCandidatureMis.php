<?php

namespace App\Events;

use App\Models\Candidature;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatutCandidatureMis
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Candidature $candidature,public string $ancienStatut, public string $nouveauStatut)
    {

    }


}
