<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidature;
use App\Models\Offre;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    public function candidater(Request $request, $offreId)
    {
        $profil = Auth::user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Créez un profil avant de postuler'], 422);
        }

        $offre = Offre::findOrFail($offreId);

        if (!$offre->actif) {
            return response()->json(['message' => 'Offre non active'], 422);
        }

        $dejaPostule = Candidature::where('offre_id', $offreId)
            ->where('profil_id', $profil->id)
            ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé'], 422);
        }

        $request->validate(['message' => 'required|string']);

        $candidature = Candidature::create([
            'offre_id'  => $offreId,
            'profil_id' => $profil->id,
            'message'   => $request->message,
            'statut'    => 'en_attente',
        ]);

        return response()->json($candidature, 201);
    }

    public function mesCandidatures()
    {
        $profil = Auth::user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Pas de profil'], 404);
        }

        $candidatures = Candidature::where('profil_id', $profil->id)->get();

        $candidatures->transform(function ($candidature) {
            $candidature->details_offre = Offre::find($candidature->offre_id);
            return $candidature;
        });

        return response()->json($candidatures);
    }

    public function candidaturesRecues($offreId)
    {
        $offre = Offre::findOrFail($offreId);

        if (Auth::id() !== $offre->user_id) {
            return response()->json(['message' => 'Interdit - Vous n\'êtes pas l\'auteur de cette offre'], 403);
        }

        $candidatures = Candidature::where('offre_id', $offreId)->get();

        $candidatures->transform(function ($candidature) {
            $candidature->profil = Profil::with('user:id,name')->find($candidature->profil_id);
            return $candidature;
        });

        return response()->json($candidatures, 200);
    }

    public function changerStatut(Request $request, $id)
    {
        $candidature = Candidature::findOrFail($id);
        $offre = Offre::findOrFail($candidature->offre_id);

        if (Auth::id() !== $offre->user_id) {
            return response()->json(['message' => 'Interdit'], 403);
        }

        $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee'
        ]);

        $candidature->update(['statut' => $request->statut]);

        return response()->json($candidature);
    }
}