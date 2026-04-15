<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = [
        'offre_id',
        'profil_id',
        'message',
        'statut',
    ];

    public function offres()
    {
        return $this->belongsToMany(Offre::class);
    }

    public function profils()
    {
        return $this->belongsToMany(Profil::class);
    }
}
