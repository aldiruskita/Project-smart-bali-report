<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Keyword mapping for rule-based classification.
     * Each category has weighted keywords with confidence scores.
     */
    private array $keywordMap = [
        'Jalan Rusak' => [
            'keywords' => ['jalan', 'lubang', 'aspal', 'trotoar', 'berlubang', 'retak', 'rusak jalan', 'jalanan', 'paving', 'jembatan'],
            'weight' => 4,
        ],
        'Sampah' => [
            'keywords' => ['sampah', 'kotor', 'bau', 'limbah', 'jorok', 'menumpuk', 'tpa', 'plastik', 'berserakan', 'busuk'],
            'weight' => 3,
        ],
        'Banjir' => [
            'keywords' => ['banjir', 'genangan', 'air naik', 'terendam', 'meluap', 'air pasang', 'kebanjiran', 'rob', 'hujan'],
            'weight' => 5,
        ],
        'Lampu Jalan' => [
            'keywords' => ['lampu', 'pju', 'gelap', 'penerangan', 'listrik padam', 'lampu mati', 'tidak menyala', 'malam gelap'],
            'weight' => 2,
        ],
        'Pohon Tumbang' => [
            'keywords' => ['pohon', 'tumbang', 'dahan', 'ranting', 'akar', 'roboh', 'pohon besar', 'tertimpa pohon'],
            'weight' => 4,
        ],
        'Drainase' => [
            'keywords' => ['drainase', 'selokan', 'got', 'saluran', 'gorong', 'tersumbat', 'parit', 'irigasi', 'air menggenang'],
            'weight' => 3,
        ],
        'Fasilitas Umum' => [
            'keywords' => ['fasilitas', 'taman', 'bangku', 'halte', 'toilet', 'wc', 'mushola', 'masjid', 'sekolah', 'puskesmas', 'ayunan', 'jembatan penyeberangan'],
            'weight' => 2,
        ],
    ];

    /**
     * ═══════════════════════════════════════════════════
     * LEVEL 1: Rule-Based AI Classification (Wajib)
     * Menggunakan keyword matching dengan confidence scoring
     * ═══════════════════════════════════════════════════
     */
    public function classifyReport(string $text): array
    {
        $text = strtolower(trim($text));
        $scores = [];

        foreach ($this->keywordMap as $categoryName => $config) {
            $matchCount = 0;
            $matchedKeywords = [];

            foreach ($config['keywords'] as $keyword) {
                if (str_contains($text, $keyword)) {
                    $matchCount++;
                    $matchedKeywords[] = $keyword;
                }
            }

            if ($matchCount > 0) {
                // Confidence = (matched / total keywords) * 100, boosted by weight
                $baseConfidence = ($matchCount / count($config['keywords'])) * 100;
                $weightedConfidence = min(99, $baseConfidence * (1 + ($config['weight'] / 10)));

                $scores[$categoryName] = [
                    'confidence' => round($weightedConfidence, 2),
                    'matches' => $matchCount,
                    'keywords' => $matchedKeywords,
                ];
            }
        }

        if (empty($scores)) {
            Log::info('[AI Classification] No match found', ['text' => substr($text, 0, 100)]);
            return [
                'category' => null,
                'confidence' => 0,
                'method' => 'rule_based',
                'all_scores' => [],
            ];
        }

        // Sort by confidence descending
        uasort($scores, fn($a, $b) => $b['confidence'] <=> $a['confidence']);

        $topCategory = array_key_first($scores);
        $topScore = $scores[$topCategory];

        Log::info('[AI Classification] Rule-based result', [
            'category' => $topCategory,
            'confidence' => $topScore['confidence'],
            'matched_keywords' => $topScore['keywords'],
            'text_preview' => substr($text, 0, 100),
        ]);

        return [
            'category' => $topCategory,
            'confidence' => $topScore['confidence'],
            'method' => 'rule_based',
            'matched_keywords' => $topScore['keywords'],
            'all_scores' => $scores,
        ];
    }

    /**
     * ═══════════════════════════════════════════════════
     * LEVEL 2: OpenAI API Classification (Advanced)
     * Menggunakan GPT untuk NLP classification
     * ═══════════════════════════════════════════════════
     */
    public function classifyWithAI(string $text): array
    {
        $apiKey = config('services.openai.key');

        if (!$apiKey) {
            Log::warning('[AI Classification] OpenAI API key not configured, skipping');
            return ['category' => null, 'confidence' => 0, 'method' => 'openai_unavailable'];
        }

        try {
            $categories = Category::pluck('name')->implode(', ');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Kamu adalah AI yang mengklasifikasikan laporan masalah lingkungan. Kategori yang tersedia: {$categories}. Jawab HANYA dengan format JSON: {\"category\": \"nama kategori\", \"confidence\": 85}"
                    ],
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ],
                'temperature' => 0.1,
                'max_tokens' => 50,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                $parsed = json_decode($content, true);

                if ($parsed && isset($parsed['category'])) {
                    Log::info('[AI Classification] OpenAI result', [
                        'category' => $parsed['category'],
                        'confidence' => $parsed['confidence'] ?? 80,
                        'text_preview' => substr($text, 0, 100),
                    ]);

                    return [
                        'category' => $parsed['category'],
                        'confidence' => $parsed['confidence'] ?? 80,
                        'method' => 'openai',
                    ];
                }
            }

            Log::warning('[AI Classification] OpenAI response parse failed', [
                'response' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error('[AI Classification] OpenAI API error', [
                'error' => $e->getMessage()
            ]);
        }

        return ['category' => null, 'confidence' => 0, 'method' => 'openai_error'];
    }

    /**
     * ═══════════════════════════════════════════════════
     * SMART CLASSIFICATION (Combined)
     * Rule-based first → OpenAI as fallback
     * ═══════════════════════════════════════════════════
     */
    public function smartClassify(string $text): array
    {
        // Level 1: Rule-based classification
        $result = $this->classifyReport($text);

        // If confidence is high enough (>= 40%), use rule-based result
        if ($result['category'] && $result['confidence'] >= 40) {
            return $result;
        }

        // Level 2: Fallback to OpenAI if rule-based confidence is low
        $aiResult = $this->classifyWithAI($text);
        if ($aiResult['category']) {
            return $aiResult;
        }

        // Return rule-based even if low confidence (better than nothing)
        return $result;
    }

    /**
     * Resolve category name to Category model ID.
     */
    public function resolveCategoryId(?string $categoryName): ?int
    {
        if (!$categoryName) return null;

        $category = Category::where('name', 'LIKE', '%' . $categoryName . '%')->first();
        return $category?->id;
    }

    /**
     * Quick classification API endpoint data.
     */
    public function classifyForPreview(string $text): array
    {
        $result = $this->classifyReport($text);

        $categoryId = $this->resolveCategoryId($result['category']);

        return [
            'category_name' => $result['category'],
            'category_id' => $categoryId,
            'confidence' => $result['confidence'],
            'method' => $result['method'],
            'matched_keywords' => $result['matched_keywords'] ?? [],
            'all_scores' => $result['all_scores'],
        ];
    }
}
