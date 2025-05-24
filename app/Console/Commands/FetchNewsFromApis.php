<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Log;

class FetchNewsFromApis extends Command
{
    protected $signature = 'news:fetch-all';
    protected $description = 'Fetch news from The Guardian, GNews, and NY Times';

    public function handle()
    {
        $categories = Category::where('status', 'active')->get();
        Log::info('Starting news fetching process.');

        foreach ($categories as $category) {
            $keywords = $category->related_keywords
                ? array_map('trim', explode(',', $category->related_keywords))
                : [$category->name];

            foreach ($keywords as $keyword) {
                Log::info("Fetching news for category: {$category->name} with keyword: {$keyword}");

                $this->fetchFromGuardian($category, $keyword);
                $this->fetchFromGNews($category, $keyword);
                $this->fetchFromNYT($category, $keyword);
            }
        }

        Log::info('News fetching completed.');
        $this->info('News fetching completed.');
    }

    protected function fetchFromGuardian($category, $keyword)
    {
        try {
            $response = Http::get(config('services.guardian.base_url') . 'search', [
                'api-key' => config('services.guardian.api_key'),
                'q' => $keyword,
                'page-size' => 10,
                'show-fields' => 'trailText,body,thumbnail,byline'
            ]);
            Log::info('Guardian API response: ' . json_encode($response));
            foreach ($response['response']['results'] ?? [] as $article) {
                News::updateOrCreate(
                    ['source_id' => $article['id']],
                    [
                        'category_id' => $category->id,
                        'title' => $article['webTitle'],
                        'url' => $article['webUrl'],
                        'summary' => $article['fields']['trailText'] ?? null,
                        'content' => $article['fields']['body'] ?? null,
                        'image_url' => $article['fields']['thumbnail'] ?? null,
                        'author' => $article['fields']['byline'] ?? null,
                        'published_at' => Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s') ?? now(),
                        'source_name' => 'guardian',
                        'news_type' => $this->detectNewsType($article, $keyword, 'guardian'),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error("Guardian API failed: " . $e->getMessage());
        }
    }

    protected function fetchFromGNews($category, $keyword)
    {
        try {
            $response = Http::get(config('services.gnews.base_url') . 'search', [
                'q' => $keyword,
                'lang' => 'en',
                'max' => 10,
                'apikey' => config('services.gnews.api_key'),
            ]);
            Log::info('GNews API response: ' . json_encode($response));
            foreach ($response['articles'] ?? [] as $article) {
                News::updateOrCreate(
                    ['source_id' => $article['url']],
                    [
                        'category_id' => $category->id,
                        'title' => $article['title'],
                        'url' => $article['url'],
                        'summary' => $article['description'] ?? null,
                        'content' => $article['content'] ?? null,
                        'image_url' => $article['image'] ?? null,
                        'author' => $article['source']['name'] ?? null,
                        'published_at' => Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') ?? now(),
                        'source_name' => 'gnews',
                        'news_type' => $this->detectNewsType($article, $keyword, 'gnews'),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error("GNews API failed: " . $e->getMessage());
        }
    }

    protected function fetchFromNYT($category, $keyword)
    {
        try {
            $response = Http::get(config('services.nyt.base_url') . 'search/v2/articlesearch.json', [
                'q' => $keyword,
                'api-key' => config('services.nyt.api_key'),
            ]);
            Log::info('NYT API response: ' . json_encode($response));
            foreach ($response['response']['docs'] ?? [] as $article) {
                $image = null;
                if (!empty($article['multimedia'])) {
                    $firstImage = collect($article['multimedia'])->first();
                    if ($firstImage && isset($firstImage['url'])) {
                        $image = 'https://www.nytimes.com/' . ltrim($firstImage['url'], '/');
                    }
                }

                News::updateOrCreate(
                    ['source_id' => $article['_id']],
                    [
                        'category_id' => $category->id,
                        'title' => $article['headline']['main'] ?? 'No Title',
                        'url' => $article['web_url'],
                        'summary' => $article['snippet'] ?? null,
                        'content' => $article['lead_paragraph'] ?? null,
                        'image_url' => $image,
                        'author' => $article['byline']['original'] ?? null,
                        'published_at' => Carbon::parse($article['pub_date'])->format('Y-m-d H:i:s') ?? now(),
                        'source_name' => 'nyt',
                        'news_type' => $this->detectNewsType($article, $keyword, 'nyt'),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error("NYT API failed: " . $e->getMessage());
        }
    }


    protected function detectNewsType($article = null, $keyword = null, $source = null)
    {
        $keyword = strtolower($keyword ?? '');
        $title = strtolower($article['title'] ?? $article['webTitle'] ?? '');
        $section = strtolower($article['sectionName'] ?? '');
        $tags = collect($article['tags'] ?? [])->pluck('id')->implode(',');

        // Parse published_at if available
        $publishedAt = $article['publishedAt'] ?? $article['webPublicationDate'] ?? $article['pub_date'] ?? null;
        $publishedTime = $publishedAt ? \Carbon\Carbon::parse($publishedAt) : null;

        // 1. Keyword-based logic
        if (str_contains($keyword, 'trend') || str_contains($title, 'trending') || str_contains($tags, 'trend'))
            return 'trending';

        if (str_contains($keyword, 'head') || str_contains($title, 'breaking') || str_contains($section, 'top') || str_contains($tags, 'breaking'))
            return 'headline';

        // 2. Time-based logic
        if ($publishedTime) {
            $now = now();
            if ($publishedTime->diffInHours($now) <= 5) {
                return 'headline'; // Very recent = headline
            }

            if ($publishedTime->diffInHours($now) <= 24) {
                return 'trending'; // Within a day = trending
            }
        }

        // 3. Source-based logic fallback
        if ($source === 'nyt' && str_contains($section, 'front'))
            return 'headline';
        if ($source === 'guardian' && str_contains($section, 'main'))
            return 'headline';

        return 'newsfeed'; // Default fallback
    }

}
