<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Playlist;

class SongController extends Controller
{
    public function index(Request $request)
    {
        $songs = Song::where('user_id', auth()->id())
            ->when($request->genre, fn($q) => $q->where('genre', $request->genre))
            ->orderBy('created_at', 'desc')
            ->get();

        // Kung AJAX request (fetch), ibalik JSON pati playlists
        if ($request->expectsJson()) {
            return response()->json([
                'songs' => $songs,
                'playlists' => Playlist::where('user_id', auth()->id())->get(['id', 'title']),
            ]);
        }

        // Normal page load, ibalik view
        $playlists = Playlist::where('user_id', auth()->id())->get(['id', 'title']);
        return view('songs.index', compact('songs', 'playlists'));
    }

    // ← BAGO: para sa genre tabs
    public function getGenres()
    {
        $genres = Song::where('user_id', auth()->id())
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre');

        return response()->json($genres);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'artist'      => 'required|string|max:255',
            'genre'       => 'nullable|string|max:100',
            'album'       => 'nullable|string|max:255',
            'youtube_url' => 'required|url',
        ]);

        Song::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'artist'      => $request->artist,
            'genre'       => $request->genre,
            'album'       => $request->album,
            'youtube_url' => $request->youtube_url,
        ]);

        return back()->with('success', 'Song added!');
    }

    public function update(Request $request, Song $song)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'artist'      => 'required|string|max:255',
            'genre'       => 'nullable|string|max:100',
            'album'       => 'nullable|string|max:255',
            'youtube_url' => 'required|url',
        ]);

        $song->update($request->only('title', 'artist', 'genre', 'album', 'youtube_url'));
        return back()->with('success', 'Song updated!');
    }

    public function destroy(Song $song)
    {
        $song->delete();
        return back()->with('success', 'Song deleted!');
    }
}