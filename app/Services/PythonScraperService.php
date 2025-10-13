<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonScraperService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.python_scraper.url', env('PYTHON_SCRAPER_URL'));
    }

    public function scrapeEpisodeEmbeds($episodeUrl, $streams)
    {
        $endpoint = $this->baseUrl . '/scrape/episode';
        $response = Http::timeout(300)->post($endpoint, [
            'episode_url' => $episodeUrl,
            'streams' => $streams
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Python API failed', ['response' => $response->body()]);
        return null;
    }

    public function getEmbed(string $episodeUrl, string $dataContent): ?string
    {
        try {
            $response = Http::timeout(120)
                ->post("{$this->baseUrl}/scrape", [
                    'episode_url' => $episodeUrl,
                    'data_content' => $dataContent,
                ]);

            if ($response->failed()) {
                Log::error("❌ Python scraper request failed: {$response->body()}");
                return null;
            }

            $json = $response->json();
            Log::info($json);
            if (isset($json['iframe'])) {
                return $json['iframe'];
            }

            Log::warning("⚠️ No iframe found in scraper response: " . json_encode($json));
            return null;
        } catch (\Throwable $e) {
            Log::error("Python scraper exception: " . $e->getMessage());
            return null;
        }
    }
}
