<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     * Statistiques globales de la plateforme
     */
    public function index()
    {
        $total = Review::count();

        if ($total === 0) {
            return response()->json([
                'total_reviews'      => 0,
                'average_score'      => 0,
                'sentiment_breakdown' => ['positive' => 0, 'neutral' => 0, 'negative' => 0],
                'top_topics'         => [],
                'recent_reviews'     => [],
            ]);
        }

        // Répartition des sentiments en %
        $sentimentCounts = Review::select('sentiment', DB::raw('count(*) as count'))
            ->groupBy('sentiment')
            ->pluck('count', 'sentiment')
            ->toArray();

        $sentimentBreakdown = [
            'positive' => round((($sentimentCounts['positive'] ?? 0) / $total) * 100, 1),
            'neutral'  => round((($sentimentCounts['neutral']  ?? 0) / $total) * 100, 1),
            'negative' => round((($sentimentCounts['negative'] ?? 0) / $total) * 100, 1),
        ];

        // Top 3 thèmes
        $allTopics = Review::whereNotNull('topics')->pluck('topics')->flatten()->toArray();
        $topicCounts = array_count_values($allTopics);
        arsort($topicCounts);
        $topTopics = array_slice(array_keys($topicCounts), 0, 3);

        // Note moyenne
        $averageScore = round(Review::avg('score'), 1);

        // 5 avis les plus récents
        $recentReviews = Review::with('user:id,name')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'total_reviews'       => $total,
            'average_score'       => $averageScore,
            'sentiment_breakdown' => $sentimentBreakdown,
            'top_topics'          => $topTopics,
            'recent_reviews'      => $recentReviews,
        ]);
    }
}
