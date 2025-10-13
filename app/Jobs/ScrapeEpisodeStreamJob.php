<?php

namespace App\Jobs;

use App\Models\EpisodeStream;
use App\Services\PythonScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapeEpisodeStreamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $streamId;

    public function __construct(int $streamId)
    {
        $this->streamId = $streamId;
    }

    public function handle(PythonScraperService $python)
    {
        $stream = EpisodeStream::find($this->streamId);
        if (! $stream) {
            Log::warning("Stream ID {$this->streamId} not found.");
            return;
        }

        $episode = $stream->episode;
        if (! $episode) {
            Log::warning("Stream {$stream->id} has no associated episode.");
            return;
        }

        Log::info("🎬 Scraping iframe for [{$stream->server_name}] {$stream->quality} from {$episode->episode_page_url}");

        $iframe = $python->getEmbed($episode->episode_page_url, $stream->data_content);
        if ($iframe) {
            $stream->update(['embed_html' => $iframe]);
            Log::info("✅ Embed iframe saved for stream ID {$stream->id}");
        } else {
            Log::warning("⚠️ No iframe found for stream ID {$stream->id}");
        }
    }
}
