<?php

use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// ── Authentification (public) ──────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ── Routes protégées (JWT Sanctum) ────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Avis clients — CRUD complet
    Route::apiResource('reviews', ReviewController::class);

    // Analyse IA — texte libre
    Route::post('/analyze', [AnalyzeController::class, 'analyze']);

    // Dashboard — statistiques globales
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
