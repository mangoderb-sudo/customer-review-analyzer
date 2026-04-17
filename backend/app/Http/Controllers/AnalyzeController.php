<?php

namespace App\Http\Controllers;

use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;

class AnalyzeController extends Controller
{
    public function __construct(private SentimentAnalyzer $analyzer) {}

    /**
     * POST /api/analyze
     * Analyse un texte libre sans créer d'avis
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|min:3',
        ]);

        $result = $this->analyzer->analyze($validated['text']);

        return response()->json($result);
    }
}
