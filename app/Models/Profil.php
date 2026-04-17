<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'titre',
        'bio',
        'localisation',
        'disponible',
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Competences()
    {
        return $this->belongsToMany(Competence::class)->withPivot('niveau');
    }
}
