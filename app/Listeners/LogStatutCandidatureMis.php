<?php

namespace App\Listeners;

use App\Events\StatutCandidatureMis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogStatutCandidatureMis
{
    public function handle(StatutCandidatureMis $event): void
    {
        $date = now()->toDateTimeString();

        Log::channel('candidatures')->info(
            "[{$date}] Statut modifié — Ancien: {$event->ancienStatut} | Nouveau: {$event->nouveauStatut}"
        );
    }
}
