<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\PythonScraperService;
use App\Models\Episode;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeEpisodeEmbedsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;
    public function __construct(public int $episodeId) {}

    public function handle(PythonScraperService $python)
    {
        $episode = Episode::with('streams')->find($this->episodeId);
        if (! $episode) return;

        $result = $python->scrapeEpisodeEmbeds(
            $episode->episode_page_url,
            $episode->streams->map(fn($s) => [
                'server_name' => $s->server_name,
                'quality' => $s->quality,
                'data_content' => $s->data_content,
            ])->toArray()
        );


        if (! $result || empty($result['embeds'])) return;

        foreach ($result['embeds'] as $embed) {
            if (empty($embed['iframe'])) continue;

            $episode->streams()
                ->where('server_name', $embed['server_name'])
                ->where('quality', $embed['quality'])
                ->update(['embed_html' => $embed['iframe']]);
        }
    }
}
