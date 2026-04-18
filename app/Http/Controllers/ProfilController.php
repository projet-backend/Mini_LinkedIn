<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function monProfil()
    {
        $profil = Auth::user()->profil()->with('competences')->first();

        if (!$profil)
            return response()->json(['message' => 'Pas de profil'], 404);

        return response()->json($profil);
    }

    public function creerProfil(Request $request)
    {
        if (Auth::user()->profil)
            return response()->json(['message' => 'Profil déjà existant'], 422);

        $data = $request->validate([
            'titre'        => 'required|string',
            'bio'          => 'nullable|string',
            'localisation' => 'required|string',
            'disponible'   => 'boolean',
        ]);

        $profil = Auth::user()->profil()->create($data);
        return response()->json($profil, 201);
    }

    public function modifierProfil(Request $request)
    {
        $profil = Auth::user()->profil()->firstOrFail();

        $data = $request->validate([
            'titre'        => 'sometimes|string',
            'bio'          => 'sometimes|string',
            'localisation' => 'sometimes|string',
            'disponible'   => 'sometimes|boolean',
        ]);

        $profil->update($data);
        return response()->json($profil);
    }

    public function ajouterCompetence(Request $request)
    {
        $profil = Auth::user()->profil()->firstOrFail();

        $request->validate([
            'competence_id' => 'required|exists:competences,id',
            'niveau'        => 'required|in:débutant,intermédiaire,expert',
        ]);

        $profil->competences()->syncWithoutDetaching([
            $request->competence_id => ['niveau' => $request->niveau]
        ]);

        return response()->json(['message' => 'Compétence ajoutée'],201);
    }

    public function supprimerCompetence($id)
    {
        $profil = Auth::user()->profil()->firstOrFail();
        $profil->competences()->detach($id);
        return response()->json(['message' => 'Compétence retirée']);
    }
}