<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MovieScraperService;
use Illuminate\Support\Str;

class ScrapeOtakudesu extends Command
{
    protected $signature = 'scrape:otaku {pages=1}';
    protected $description = 'Scrape otakudesu.best and import into local DB';

    protected MovieScraperService $scraper;

    public function __construct(MovieScraperService $scraper)
    {
        parent::__construct();
        $this->scraper = $scraper;
    }

    public function handle()
    {
        $pages = (int)$this->argument('pages');
        $this->info("Start scraping {$pages} pages...");
        for ($p = 1; $p <= $pages; $p++) {
            $this->info("Scraping listing page {$p}");
            $url = $p === 1 ? 'https://otakudesu.best/ongoing-anime/' : "https://otakudesu.best/ongoing-anime/page/{$p}/";
            $items = $this->scraper->scrapeListing($url);
            foreach ($items as $item) {
                $this->info(" -> Fetching detail: {$item['title']}");
                $detail = $this->scraper->scrapeDetail($item['url']);
                if (!$detail) {
                    $this->error("Failed to fetch {$item['url']}");
                    continue;
                }
                $movie = $this->scraper->saveToDatabase($detail);
                if ($movie) $this->info("Saved: {$movie->title}");
                else $this->error("Save failed for {$item['title']}");
            }

            // optional throttle
            sleep(2);
        }

        $this->info('Scraping finished.');
        return 0;
    }
}
