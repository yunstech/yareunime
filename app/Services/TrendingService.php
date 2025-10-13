<?php

namespace App\Services;

use App\Models\{Movie, View, Favorite, Comment};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrendingService
{
    /**
     * Hitung skor trending untuk semua film
     */
    public function calculateTrendingScores(): void
    {
        $startDate = Carbon::now()->subDays(7);

        // Hitung view count 7 hari terakhir
        $viewScores = View::select('movie_id', DB::raw('COUNT(*) * 3 AS score'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('movie_id');

        // Hitung favorite count
        $favoriteScores = Favorite::select('movie_id', DB::raw('COUNT(*) * 2 AS score'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('movie_id');

        // Hitung comment count
        $commentScores = Comment::select('target_id AS movie_id', DB::raw('COUNT(*) * 1 AS score'))
            ->where('target_type', Movie::class)
            ->where('created_at', '>=', $startDate)
            ->groupBy('target_id');

        // Gabungkan semua skor
        $merged = $viewScores
            ->unionAll($favoriteScores)
            ->unionAll($commentScores);

        // Aggregate total skor per movie_id
        $totals = DB::table(DB::raw("({$merged->toSql()}) AS sub"))
            ->mergeBindings($merged)
            ->select('movie_id', DB::raw('SUM(score) AS total_score'))
            ->groupBy('movie_id')
            ->get();

        // Update skor ke table movies
        foreach ($totals as $item) {
            $movie = Movie::find($item->movie_id);
            if ($movie) {
                $movie->update(['trending_score' => $item->total_score + ($movie->rating / 2)]);
            }
        }
    }

    /**
     * Ambil film trending berdasarkan periode waktu
     */
    public function getTrending(string $period = 'weekly', int $limit = 10)
    {
        $query = Movie::query();

        switch ($period) {
            case 'daily':
                $query->where('updated_at', '>=', now()->subDay());
                break;
            case 'monthly':
                $query->where('updated_at', '>=', now()->subMonth());
                break;
            default:
                $query->where('updated_at', '>=', now()->subDays(7));
                break;
        }

        return $query->orderByDesc('trending_score')
            ->take($limit)
            ->get();
    }
}
