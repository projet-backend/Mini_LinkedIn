<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offre;
use Illuminate\Support\Facades\Auth;

class OffreController extends Controller
{
    public function index(Request $request)
    {
        $query = Offre::where('actif', true)
            ->orderBy('created_at', 'desc');

        if ($request->type)
            $query->where('type', $request->type);

        if ($request->localisation)
            $query->where('localisation', $request->localisation);

        return response()->json($query->paginate(10));
    }

    public function show($id)
    {
        return response()->json(Offre::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre'        => 'required|string',
            'description'  => 'required|string',
            'localisation' => 'required|string',
            'type'         => 'required|in:CDI,CDD,stage',
        ]);

        $data['user_id'] = Auth::id();
        $data['actif']   = true;

        return response()->json(Offre::create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $offre = Offre::findOrFail($id);

        if (Auth::id() !== $offre->user_id)
            return response()->json(['message' => 'Interdit'], 403);

        $data = $request->validate([
            'titre'        => 'sometimes|string',
            'description'  => 'sometimes|string',
            'localisation' => 'sometimes|string',
            'type'         => 'sometimes|in:CDI,CDD,stage',
        ]);

        $offre->update($data);
        return response()->json($offre);
    }

    public function destroy($id)
    {
        $offre = Offre::findOrFail($id);

        if (Auth::id() !== $offre->user_id)
            return response()->json(['message' => 'Interdit'], 403);

        $offre->delete();
        return response()->json(['message' => 'Offre supprimée']);
    }
}