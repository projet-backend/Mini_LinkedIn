<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\AdminController;

//Publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

//Protégées par JWT
Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // PARTIE 3.1
    Route::middleware('role:candidat')->group(function () {
        Route::post('/profil',[ProfilController::class, 'creerProfil']);
        Route::get('/profil',[ProfilController::class, 'monProfil']);
        Route::put('/profil',[ProfilController::class, 'modifierProfil']);
        Route::post('/profil/competences',[ProfilController::class, 'ajouterCompetence']);
        Route::delete('/profil/competences/{competence}',[ProfilController::class, 'supprimerCompetence']);
        Route::post('/offres/{offre}/candidater',[CandidatureController::class, 'candidater']);
        Route::get('/mes-candidatures',[CandidatureController::class, 'mesCandidatures']);
    });

    // PARTIE 3.2
    Route::get('/offres',[OffreController::class, 'index']);
    Route::get('/offres/{offre}',[OffreController::class, 'show']);

    Route::middleware('role:recruteur')->group(function () {
        Route::post('/offres',[OffreController::class, 'store']);
        Route::put('/offres/{offre}',[OffreController::class, 'update']);
        Route::delete('/offres/{offre}',[OffreController::class, 'destroy']);
    });

    // PARTIE 3.3
    Route::middleware('role:recruteur')->group(function () {
        Route::get('/offres/{offre}/candidatures',[CandidatureController::class, 'candidaturesRecues']);
        Route::patch('/candidatures/{candidature}/statut', [CandidatureController::class, 'changerStatut']);
    });

    // PARTIE 3.4
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users',[AdminController::class, 'listeUsers']);
        Route::delete('/users/{user}',[AdminController::class, 'supprimerUser']);
        Route::patch('/offres/{offre}',[AdminController::class, 'modifierStatutOffre']);
    });
});