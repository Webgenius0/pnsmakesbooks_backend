<?php

namespace App\Jobs;

use App\Models\News;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class TranslateNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $news;
    protected $languages;

    public function __construct(News $news, $languages = ['es', 'nl'])
    {
        $this->news = $news;
        $this->languages = $languages;
    }

    public function handle()
    {
        $data = [];
        foreach ($this->languages as $lang) {
            $data[$lang] = [
                'title' => $this->translate($this->news->title, $lang),
                'summary' => $this->translate($this->news->summary, $lang),
                'content' => $this->translate($this->news->content, $lang),
            ];
        }

        $this->news->update([
            'translations' => $data,
        ]);
    }

    protected function translate($text, $to)
    {
        try {
            $response = Http::post('https://libretranslate.com/translate', [
                'q' => $text,
                'source' => 'en',
                'target' => $to,
                'format' => 'text'
            ]);
            Log::info("TranslateNewsJob::translate: " . $response->json());
            return $response->json()['translatedText'] ?? $text;
        } catch (Exception $e) {
            Log::error("TranslateNewsJob::translate: " . $e->getMessage());
            return $text;
        }
    }
}
