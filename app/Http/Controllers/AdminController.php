<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Offre;

class AdminController extends Controller
{
    public function listeUsers()
    {
        return response()->json(User::paginate(10));
    }

    public function supprimerUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }

    public function modifierStatutOffre(Request $request, $id)
    {
        $offre = Offre::findOrFail($id);

        $request->validate(['actif' => 'required|boolean']);

        $offre->update(['actif' => $request->actif]);
        return response()->json(['message' => 'Statut mis à jour']);
    }
}