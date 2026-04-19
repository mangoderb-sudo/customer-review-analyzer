<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     * Statistiques globales de la plateforme
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $total = Review::where('user_id', $userId)->count();

        if ($total === 0) {
            return response()->json([
                'total_reviews' => 0,
                'average_score' => 0,
                'positive_percentage' => 0,
                'negative_percentage' => 0,
                'top_topics' => [],
                'recent_reviews' => [],
            ]);
        }

        $sentimentCounts = Review::where('user_id', $userId)
            ->select('sentiment', DB::raw('count(*) as count'))
            ->groupBy('sentiment')
            ->pluck('count', 'sentiment')
            ->toArray();

        $allTopics = Review::where('user_id', $userId)
            ->whereNotNull('topics')->pluck('topics')->flatten()->toArray();
        $topicCounts = array_count_values($allTopics);
        arsort($topicCounts);
        $topTopics = array_slice(array_keys($topicCounts), 0, 3);

        $recentReviews = Review::where('user_id', $userId)
            ->latest()->take(5)->get();

        return response()->json([
            'total_reviews' => $total,
            'average_score' => round(Review::where('user_id', $userId)->avg('score'), 1),
            'positive_percentage' => round((($sentimentCounts['positive'] ?? 0) / $total) * 100, 1),
            'negative_percentage' => round((($sentimentCounts['negative'] ?? 0) / $total) * 100, 1),
            'top_topics' => $topTopics,
            'recent_reviews' => $recentReviews,
        ]);
    }
}
