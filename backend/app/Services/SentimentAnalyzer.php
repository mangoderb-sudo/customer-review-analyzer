<?php

namespace App\Services;

class SentimentAnalyzer
{
    // Mots positifs
    private array $positiveWords = [
    'excellent', 'parfait', 'super', 'génial', 'rapide', 'satisfait',
    'merveilleux', 'recommande', 'qualité', 'bravo', 'top', 'bien',
    'content', 'agréable', 'efficace', 'impressionné', 'fantastique',
    'magnifique', 'incroyable', 'formidable', 'adorable', 'exceptionnel',
    'remarquable', 'impeccable', 'irréprochable', 'ravie', 'ravi',
    'enchante', 'enchantée', 'adore', 'vivement', 'félicitations', 
    'great', 'good', 'fast', 'happy', 'love', 'amazing', 'best',
    'perfect', 'wonderful', 'satisfied', 'recommend', 'outstanding',
    'excellent', 'superb', 'brilliant', 'fantastic', 'awesome',
    'impressive', 'delighted', 'pleased', 'thrilled', 'enjoyed',
    ];

    // Mots négatifs
    private array $negativeWords = [
        'mauvais', 'nul', 'horrible', 'décevant', 'lent', 'cassé',
        'problème', 'pire', 'déçu', 'insatisfait', 'jamais', 'arnaque',
        'terrible', 'catastrophe', 'défectueux', 'inutile', 'scandaleux',
        'bad', 'slow', 'broken', 'terrible', 'worst', 'disappointed',
        'awful', 'poor', 'useless', 'never', 'horrible', 'defective',
    ];

    // Thèmes et leurs mots-clés associés
    private array $topics = [
        'livraison'  => ['livraison', 'délai', 'expédition', 'reçu', 'colis', 'delivery', 'shipping', 'arrived'],
        'qualité'    => ['qualité', 'matière', 'solide', 'durable', 'quality', 'material', 'solid', 'build'],
        'prix'       => ['prix', 'cher', 'coût', 'valeur', 'abordable', 'price', 'cost', 'value', 'cheap', 'expensive'],
        'service'    => ['service', 'support', 'assistance', 'réponse', 'help', 'customer', 'response', 'staff'],
        'rapidité'   => ['rapide', 'vite', 'immédiat', 'instantané', 'fast', 'quick', 'speed', 'instant'],
    ];

    /**
     * Analyse complète d'un texte : sentiment + score + thèmes
     */
    public function analyze(string $text): array
    {
        $text = strtolower($text);
        $words = preg_split('/\s+/', $text);

        $positiveCount = $this->countMatches($words, $this->positiveWords);
        $negativeCount = $this->countMatches($words, $this->negativeWords);

        $sentiment = $this->classifySentiment($positiveCount, $negativeCount);
        $score     = $this->computeScore($text, $positiveCount, $negativeCount);
        $topics    = $this->extractTopics($text);

        return [
            'sentiment' => $sentiment,
            'score'     => $score,
            'topics'    => $topics,
        ];
    }

    /**
     * Compte les occurrences de mots d'une liste dans le texte
     */
    private function countMatches(array $words, array $list): int
    {
        return count(array_filter($words, fn($w) => in_array($w, $list)));
    }

    /**
     * Classifie le sentiment selon le ratio positif/négatif
     */
    private function classifySentiment(int $pos, int $neg): string
    {
        if ($pos === 0 && $neg === 0) return 'neutral';
        if ($pos > $neg)              return 'positive';
        if ($neg > $pos)              return 'negative';
        return 'neutral';
    }

    /**
     * Calcule un score de satisfaction entre 0 et 100
     * Basé sur : ratio mots positifs/négatifs + longueur du texte
     */
    private function computeScore(string $text, int $pos, int $neg): int
    {
        $base = 50;

        // Influence des mots positifs/négatifs
        $base += ($pos * 10);
        $base -= ($neg * 10);

        // Bonus léger pour les textes détaillés (engagement)
        $wordCount = str_word_count($text);
        if ($wordCount > 20) $base += 5;
        if ($wordCount > 50) $base += 5;

        // Malus si ponctuation négative (!!!  ou ???)
        if (preg_match('/[!]{2,}/', $text)) $base -= 5;

        return max(0, min(100, $base));
    }

    /**
     * Détecte les thèmes présents dans le texte
     */
    private function extractTopics(string $text): array
    {
        $detected = [];

        foreach ($this->topics as $topic => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    $detected[] = $topic;
                    break;
                }
            }
        }

        return $detected;
    }
}
