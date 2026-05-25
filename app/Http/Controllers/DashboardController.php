<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Song;
use App\Models\Genre;
use App\Models\Playlist;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers     = User::count();
        $totalSongs     = Song::count();
        $totalGenres    = Genre::count();
        $totalPlaylists = Playlist::count();

        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        $userMonthly = User::selectRaw("MONTH(created_at) as m, COUNT(*) as c")
            ->whereYear('created_at', date('Y'))
            ->groupBy('m')
            ->pluck('c', 'm');
        $userChartData = array_map(fn($m) => $userMonthly[$m] ?? 0, range(1, 12));

        $songMonthly = Song::selectRaw("MONTH(created_at) as m, COUNT(*) as c")
            ->whereYear('created_at', date('Y'))
            ->groupBy('m')
            ->pluck('c', 'm');
        $songChartData = array_map(fn($m) => $songMonthly[$m] ?? 0, range(1, 12));

        $genreSongs = Song::select('genre', DB::raw('COUNT(*) as c'))
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->groupBy('genre')
            ->orderByDesc('c')
            ->get();

        $genreLabels = $genreSongs->pluck('genre')->toArray();
        $genreData   = $genreSongs->pluck('c')->toArray();
        $genreColors = ['#FF69B4','#a855f7','#f59e0b','#22d3ee','#10b981','#6b7280','#ef4444','#8b5cf6','#ec4899','#14b8a6'];

        $topGenres = Song::select('genre', DB::raw('COUNT(*) as total'))
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->groupBy('genre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $maxGenre = $topGenres->max('total') ?: 1;

        $totalWithGenre = Song::whereNotNull('genre')->where('genre', '!=', '')->count();
        $othersCount = Song::count() - $totalWithGenre;
        if ($othersCount > 0) {
            $genreLabels[] = 'Others';
            $genreData[]   = $othersCount;
        }

        return view('dashboard', compact(
            'totalUsers', 'totalSongs', 'totalGenres', 'totalPlaylists',
            'months', 'userChartData', 'songChartData',
            'genreLabels', 'genreData', 'genreColors',
            'topGenres', 'maxGenre'
        ));
    }
}
