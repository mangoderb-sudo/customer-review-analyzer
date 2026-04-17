<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private SentimentAnalyzer $analyzer) {}

    /**
     * GET /api/reviews
     */
    public function index()
    {
        $reviews = Review::with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * POST /api/reviews
     * L'analyse IA se déclenche automatiquement à la création
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        // Analyse IA automatique
        $analysis = $this->analyzer->analyze($validated['content']);

        $review = Review::create([
            'user_id'   => $request->user()->id,
            'content'   => $validated['content'],
            'sentiment' => $analysis['sentiment'],
            'score'     => $analysis['score'],
            'topics'    => $analysis['topics'],
        ]);

        return response()->json($review, 201);
    }

    /**
     * GET /api/reviews/{id}
     */
    public function show(Review $review)
    {
        return response()->json($review->load('user:id,name'));
    }

    /**
     * PUT /api/reviews/{id}
     */
    public function update(Request $request, Review $review)
    {
        // Seul l'auteur ou un admin peut modifier
        if ($request->user()->id !== $review->user_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        // Re-analyse IA après modification
        $analysis = $this->analyzer->analyze($validated['content']);

        $review->update([
            'content'   => $validated['content'],
            'sentiment' => $analysis['sentiment'],
            'score'     => $analysis['score'],
            'topics'    => $analysis['topics'],
        ]);

        return response()->json($review);
    }

    /**
     * DELETE /api/reviews/{id}
     */
    public function destroy(Request $request, Review $review)
    {
        if ($request->user()->id !== $review->user_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Avis supprimé.']);
    }
}
