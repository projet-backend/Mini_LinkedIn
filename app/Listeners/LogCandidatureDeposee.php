<?php

namespace App\Listeners;

use App\Events\CandidatureDeposee;
use Illuminate\Support\Facades\Log;

class LogCandidatureDeposee
{
    public function handle(CandidatureDeposee $event): void
    {
        $candidature = $event->candidature->load('profil.user', 'offre');

        $nomCandidat = $candidature->profil?->user?->name ?? 'Inconnu';
        $titreOffre  = $candidature->offre?->titre ?? 'Inconnu';
        $date        = now()->toDateTimeString();

        Log::channel('candidatures')->info(
            "[{$date}] Candidature déposée — Candidat: {$nomCandidat} | Offre: {$titreOffre}"
        );
    }
}
