<?php

namespace App\Http\Controllers;

use App\Services\TrendingService;

class TrendingController extends Controller
{
    protected $trendingService;

    public function __construct(TrendingService $trendingService)
    {
        $this->trendingService = $trendingService;
    }

    public function index()
    {
        $movies = $this->trendingService->getTrending('weekly', 12);
        return view('top', [
            'movies' => $movies,
            'filter' => 'weekly',
        ]);
    }

    public function recalculate()
    {
        $this->trendingService->calculateTrendingScores();
        return back()->with('success', 'Trending movies recalculated!');
    }
}
