<?php

namespace App\Services;

use App\Jobs\ScrapeEpisodeEmbedsJob;
use App\Jobs\ScrapeEpisodeStreamJob;
use App\Models\EpisodeDownload;
use App\Models\EpisodeStream;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Support\Facades\Log;

class MovieScraperService
{
    protected Client $client;
    protected string $baseUrl;
    protected int $delaySeconds = 1;

    public function __construct()
    {
        $this->baseUrl = 'https://otakudesu.best'; // ganti bila perlu
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 20.0,
            'headers'  => [
                'User-Agent' => 'YareunimeScraper/1.0 (+yourdomain.com)',
                'Accept' => 'text/html,application/xhtml+xml',
            ],
        ]);
    }

    /**
     * Scrape listing dari halaman utama / ongoing / halaman kategori.
     * Mengambil: title, url, poster (opsional)
     *
     * Selector: .thumb a (link ke detail), img src di .thumbz img, judul di .jdlflm
     * Contoh markup: lihat halaman utama upload. :contentReference[oaicite:3]{index=3}
     */
    public function scrapeListing(string $path = '/', int $page = 1): array
    {
        $url = $path;
        if ($page > 1) {
            // sesuaikan pattern jika situs menggunakan /page/X
            $url = rtrim($path, '/') . '/page/' . $page . '/';
        }


        $html = $this->get($url);
        if (! $html) return [];
        $crawler = new Crawler($html);
        $items = [];

        // cari semua thumbnail block
        $crawler->filter('.thumb a')->each(function (Crawler $node) use (&$items) {
            $parent = $node->ancestors()->first();
            $title = null;
            $img = null;

            // judul kadang di .jdlflm
            try {
                $titleNode = $node->filter('.jdlflm');
                if ($titleNode->count()) {
                    $title = trim($titleNode->text());
                }
            } catch (\Exception $e) { /* ignore */ }

            // fallback: alt attribute dari img
            try {
                if ($node->filter('img')->count()) {
                    $img = $this->absUrl($node->filter('img')->attr('src'));
                    if (!$title) $title = trim($node->filter('img')->attr('alt') ?? '');
                }
            } catch (\Exception $e) {}

            $href = $node->attr('href') ?? null;
            if ($href) {
                $items[] = [
                    'title' => $title ?: null,
                    'url' => $this->absUrl($href),
                    'poster' => $img,
                ];
            }
        });

        return $items;
    }

    /**
     * Scrape halaman detail movie/anime.
     *
     * - Judul & metadata: mengambil dari blok .infozin .infozingle (format "Judul: ...")
     * - Poster: .thumbz img (contoh di movie.html). :contentReference[oaicite:4]{index=4}
     * - Genres: link rel=tag di blok info
     * - Episodes: .episodelist ul li a (contoh list episode ada di .episodelist). :contentReference[oaicite:5]{index=5}
     */
    public function scrapeDetail(string $movieUrl): ?array
    {
        $html = $this->get($movieUrl);
        if (! $html) return null;
        $crawler = new Crawler($html);

        $poster = null;
        $background = null;

        if ($crawler->filter('.thumb img')->count()) {
            $background = $this->absUrl($crawler->filter('.thumb img')->attr('src'));
        }

        if ($crawler->filter('.fotoanime img')->count()) {
            $poster = $this->absUrl($crawler->filter('.fotoanime img')->attr('src'));
        } elseif ($crawler->filter('.venser img')->count()) {
            $poster = $this->absUrl($crawler->filter('.venser img')->attr('src'));
        }

        $meta = [];
        if ($crawler->filter('.infozin .infozingle')->count()) {
            $crawler->filter('.infozin .infozingle p')->each(function (Crawler $p) use (&$meta) {
                $text = trim($p->text());

                if (Str::contains($text, ':')) {
                    [$k, $v] = array_map('trim', explode(':', $text, 2));
                    $meta[$k] = $v;
                }
            });
        }


        $title = $meta['Judul'] ?? null;


        if (!$title) {
            if ($crawler->filter('.jdlz')->count()) {
                $title = trim($crawler->filter('.jdlz')->text());
            } elseif ($crawler->filter('h1.entry-title')->count()) {
                $title = trim($crawler->filter('h1.entry-title')->text());
            } elseif ($crawler->filter('title')->count()) {

                $title = trim(str_replace(['– Otakudesu', 'Otakudesu'], '', $crawler->filter('title')->text()));
            }
        }

        $genres = [];
        if ($crawler->filter('.infozingle a[rel="tag"]')->count()) {
            $crawler->filter('.infozingle a[rel="tag"]')->each(function (Crawler $n) use (&$genres) {
                $genres[] = trim($n->text());
            });
        }


        $episodes = [];
        if ($crawler->filter('.episodelist ul li a')->count()) {
            $crawler->filter('.episodelist ul li a')->each(function (Crawler $a) use (&$episodes) {
                $epTitle = trim($a->text());
                $epUrl = $a->attr('href');
                $episodes[] = [
                    'title' => $epTitle,
                    'url' => $epUrl ? $this->absUrl($epUrl) : null,

                ];
            });
        }

        $description = null;
        if ($crawler->filter('.sinopc')->count()) {
            $description = trim($crawler->filter('.sinopc')->text());
        }
        return compact('title', 'poster', 'background', 'description', 'genres', 'episodes', 'meta', 'movieUrl');
    }

    protected function fetchNewNonce(): ?string
    {
        $ajaxUrl = "{$this->baseUrl}/wp-admin/admin-ajax.php";

        try {
            $res = $this->client->post($ajaxUrl, [
                'form_params' => ['action' => 'aa1208d27f29ca340c92c66d1926f13f'],
                'headers' => [
                    'Referer' => $this->baseUrl,
                    'User-Agent' => 'YareunimeScraper/1.0 (+yourdomain.com)',
                ],
            ]);

            if ($res->getStatusCode() === 200) {
                // Respons bisa berupa JSON {"data":"66dfab0548"} atau langsung string "66dfab0548"
                $body = (string) $res->getBody();
                $json = json_decode($body, true);
                if (is_array($json) && isset($json['data'])) {
                    return $json['data'];
                }
                return trim($body);
            }
        } catch (\Throwable $e) {
            \Log::warning("Nonce fetch failed: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Scrape halaman episode — ambil daftar mirror / download link
     *
     * Di file contoh episode, mirror ditampilkan sebagai ul .m360p/.m480p/.m720p dengan <li><a data-content="base64"></a>
     * Selain itu ada link download safelink di body (contoh: links ke desustream/safelink).
     *
     * Return contoh:
     * [
     *   'mirrors' => [
     *     '360p' => [ ['name'=>'ondesu','data'=>'eyJpZCI6...'], ... ],
     *     '480p' => [...],
     *   ],
     *   'downloads' => [ ['label'=>'ODFiles','url'=>'https://desustream.com/...'], ... ]
     * ]
     */
    public function scrapeEpisode(string $episodeUrl): ?array
    {
        $html = $this->get($episodeUrl);
        if (! $html) return null;

        $crawler = new Crawler($html);

        // --- Thumbnail & Duration ---
        $thumbnail = null;
        if ($crawler->filter('.thumb img')->count()) {
            $thumbnail = $this->absUrl($crawler->filter('.thumb img')->attr('src'));
        } elseif ($crawler->filter('meta[property="og:image"]')->count()) {
            $thumbnail = $this->absUrl($crawler->filter('meta[property="og:image"]')->attr('content'));
        }

        $duration = null;
        $crawler->filter('p, span')->each(function (Crawler $node) use (&$duration) {
            $text = strtolower($node->text());
            if (preg_match('/(\d+)\s*(?:menit|min)/', $text, $m)) {
                $duration = (int)$m[1];
            }
        });

        $result = [
            'mirrors' => [],
            'downloads' => [],
            'embed_html' => null,
            'thumbnail' => $thumbnail,
            'duration' => $duration,
        ];

        // --- Mirrors per quality ---
        foreach (['360p','480p','720p'] as $q) {
            $class = '.m' . $q;
            if ($crawler->filter($class)->count()) {
                $crawler->filter($class . ' li a')->each(function (Crawler $a) use (&$result, $q) {
                    $name = trim($a->text());
                    $data = $a->attr('data-content') ?? $a->attr('href') ?? null;
                    if ($data) {
                        $result['mirrors'][$q][] = ['name' => $name, 'data' => $data];
                    }
                });
            }
        }

        // --- Download Links ---
        $crawler->filter('a')->each(function (Crawler $a) use (&$result) {
            $href = $a->attr('href') ?? '';
            if (Str::contains($href, ['safelink', 'desustream.com', 'odfiles', 'pdrain', 'gofile', 'mega'])) {
                $label = trim($a->text()) ?: $href;
                $result['downloads'][] = ['label' => $label, 'url' => $this->absUrl($href)];
            }
        });

        // --- Coba ambil embed player langsung ---
        if ($crawler->filter('#pembed')->count()) {
            $result['embed_html'] = $crawler->filter('#pembed')->html();
        }

        // --- Jika embed masih null → fallback pakai Panther (headless Chrome) ---
        if (empty($result['embed_html'])) {
            try {
                $panther = app(\App\Services\PantherScraperService::class);
                $pantherData = $panther->fetchEpisodeEmbed($episodeUrl);

                if ($pantherData && !empty($pantherData['embed_html'])) {
                    $result['embed_html'] = $pantherData['embed_html'];
                }
            } catch (\Throwable $e) {
                \Log::error("Panther fallback failed for {$episodeUrl}: " . $e->getMessage());
            }
        }

        return $result;
    }



    /**
     * Save scraped detail into DB (create/update)
     */
    public function saveToDatabase(array $data): ?Movie
    {
        if (empty($data['title'])) {
            Log::warning("Skipping movie save — missing title.");
            return null;
        }

        $slug = Str::slug($data['title']);

        $movie = Movie::updateOrCreate(
            ['slug' => $slug],
            [
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'poster'      => $data['poster'] ?? null,
                'type'        => 'anime',
            ]
        );

        $season = $movie->seasons()->firstOrCreate([
            'season_number' => 1,
            'title' => $movie->title . ' - Season 1',
        ]);

        // 🎭 Genres
        if (!empty($data['genres'])) {
            $genreIds = [];
            foreach ($data['genres'] as $g) {
                $genre = \App\Models\Genre::firstOrCreate(['name' => $g]);
                $genreIds[] = $genre->id;
            }
            $movie->genres()->sync($genreIds);
        }

        // 🎬 Episodes
        if (!empty($data['episodes'])) {
            foreach ($data['episodes'] as $idx => $epData) {
                $episodeNumber = $this->extractEpisodeNumber($epData['title']) ?? ($idx + 1);

                $episode = Episode::updateOrCreate(
                    ['episode_page_url' => $epData['url']],
                    [
                        'season_id'      => $season->id,
                        'episode_number' => $episodeNumber,
                        'title'          => $epData['title'],
                    ]
                );

                // 🕷️ Jalankan scraping dasar (thumbnail, durasi, mirrors)
                $scraped = $this->scrapeEpisode($epData['url']);

                if (!empty($scraped)) {
                    $episode->update([
                        'thumbnail' => $scraped['thumbnail'] ?? null,
                        'duration'  => $scraped['duration'] ?? null,
                    ]);

                    // 🎞️ Simpan stream mirrors (data_content)
                    $streams = [];
                    if (!empty($scraped['mirrors'])) {
                        foreach ($scraped['mirrors'] as $quality => $servers) {
                            foreach ($servers as $s) {
                                $stream = EpisodeStream::updateOrCreate(
                                    [
                                        'episode_id'  => $episode->id,
                                        'server_name' => $s['name'],
                                        'quality'     => $quality,
                                    ],
                                    [
                                        'data_content' => $s['data'],
                                    ]
                                );
                                $streams[] = $stream;
                            }
                        }
                    }

                    // 💾 Simpan download links
                    if (!empty($scraped['downloads'])) {
                        foreach ($scraped['downloads'] as $dl) {
                            EpisodeDownload::updateOrCreate(
                                [
                                    'episode_id' => $episode->id,
                                    'label'      => $dl['label'],
                                ],
                                [
                                    'url' => $dl['url'],
                                ]
                            );
                        }
                    }

                    // 🚀 Dispatch Python scrape hanya jika embed_html belum ada
                    $missingEmbeds = collect($streams)->filter(fn($s) => empty($s->embed_html));

                    if ($missingEmbeds->isNotEmpty()) {
                        \App\Jobs\ScrapeEpisodeEmbedsJob::dispatch($episode->id)
                            ->onQueue('scraping');
                        Log::info("📤 Dispatched scrape job for episode {$episode->id}");
                    } else {
                        Log::info("✅ Skipping embed scrape (already exists) for episode {$episode->id}");
                    }
                } else {
                    Log::warning("⚠️ Failed to scrape episode page: {$epData['url']}");
                }
            }
        }

        Log::info("✅ Saved movie: {$movie->title}");
        return $movie;
    }


    protected function absUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://','https://'])) return $url;
        return rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
    }

    protected function get(string $path): ?string
    {
        try {
            // Pastikan URL absolut
            $url = $this->absUrl($path);

            if ($this->delaySeconds > 0) {
                usleep($this->delaySeconds * 1_000_000);
            }

            // Fetch HTML
            $res = $this->client->request('GET', $url, [
                'allow_redirects' => true,
                'verify' => false, // penting kalau SSL situs error
                'timeout' => 30,
                'connect_timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ],
            ]);

            $status = $res->getStatusCode();
            if ($status !== 200) {
                Log::warning("Non-200 ({$status}) for {$url}");
                return null;
            }
            $body = (string) $res->getBody();
            if (empty($body)) {
                Log::warning("Empty body for {$url}");
                return null;
            }

            return $body;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error("ClientException for {$path}: " . $e->getMessage());
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("ConnectException for {$path}: " . $e->getMessage());
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("RequestException for {$path}: " . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error("General error fetching {$path}: " . $e->getMessage());
        }

        return null;
    }



    protected function extractEpisodeNumber(string $title): ?int
    {
        if (preg_match('/\b(?:EP|Episode|E|Ep)\s*\.?\s*#?\s*([0-9]{1,3})\b/i', $title, $m)) {
            return (int)$m[1];
        }
        if (preg_match('/\b([0-9]{1,3})\b(?!.*min)/', $title, $m)) {
            return (int)$m[1];
        }
        return null;
    }
}
