<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Playlist;
use App\Models\Song;

class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        // 1. Kunin lahat ng playlists ng user (Ginamit ang 'title' base sa model mo)
        $playlists = Playlist::where('user_id', auth()->id())
            ->withCount('songs')
            ->orderBy('created_at', 'desc')
            ->get();
    
        // 2. Kunin lahat ng kanta mula sa "Songs Tab" ng user
        $songs = Song::where('user_id', auth()->id())
            ->orderBy('title')
            ->get();

        // 3. Alamin kung anong playlist ang gustong tignan ng user gamit ang URL parameter (?playlist_id=)
       
    $firstPlaylist = $playlists->first();
    $activePlaylistId = $request->get('playlist_id') ?? ($firstPlaylist ? $firstPlaylist->id : null);
        
        $activePlaylist = null;
        $activePlaylistSongs = [];

        if ($activePlaylistId) {
            $activePlaylist = Playlist::with('songs')->find($activePlaylistId);
            if ($activePlaylist) {
                $activePlaylistSongs = $activePlaylist->songs;
            }
        }
    
        // Ipinasa lahat nang ligtas sa view (Gumagamit ng 'playlist.index')
        return view('playlist.index', compact('playlists', 'songs', 'activePlaylist', 'activePlaylistSongs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('playlist', 'public');
        }

        $playlist = Playlist::create([
            'user_id'     => auth()->id(),
            'title'       => $request->name,
            'description' => $request->description,
            'cover_image' => $coverPath,
        ]);

        // Iba-back load papunta sa ginawang playlist para diretso view agad
        return redirect()->route('playlists.index', ['playlist_id' => $playlist->id])->with('success', 'Playlist created!');
    }

    public function update(Request $request, Playlist $playlist)
    {
        $rules = [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];

        // Allow file upload OR URL string for cover_image
        if ($request->hasFile('cover_image')) {
            $rules['cover_image'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        } elseif ($request->filled('cover_image')) {
            $rules['cover_image'] = 'url';
        }

        $request->validate($rules);

        $coverPath = $playlist->cover_image;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('playlist', 'public');
        } elseif ($request->filled('cover_image') && filter_var($request->cover_image, FILTER_VALIDATE_URL)) {
            $coverPath = $request->cover_image;
        }

        $playlist->update([
            'title'       => $request->name,
            'description' => $request->description,
            'cover_image' => $coverPath,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'playlist' => $playlist]);
        }

        return back()->with('success', 'Playlist updated!');
    }

    public function destroy(Playlist $playlist)
    {
        $playlist->delete();
        return redirect()->route('playlists.index')->with('success', 'Playlist deleted!');
    }

    // Ito ang gagamitin natin sa modal para magpasok ng kanta gamit ang tradisyunal na form action
    public function addSongForm(Request $request, Playlist $playlist)
    {
        $request->validate(['song_id' => 'required|exists:songs,id']);

        if (!$playlist->songs()->where('song_id', $request->song_id)->exists()) {
            $order = $playlist->songs()->count();
            $playlist->songs()->attach($request->song_id, ['order' => $order]);
        }

        return redirect()->route('playlists.index', ['playlist_id' => $playlist->id])->with('success', 'Song added to playlist!');
    }

    // Ginawang resource form link para sa delete button sa table row
    public function removeSongForm(Playlist $playlist, Song $song)
    {
        $playlist->songs()->detach($song->id);
        return redirect()->route('playlists.index', ['playlist_id' => $playlist->id])->with('success', 'Song removed from playlist!');
    }

    /* --- PANATILIHIN ANG IYONG KASALUKUYANG AJAX ENDPOINTS --- */
    public function addSong(Request $request, Playlist $playlist)
    {
        $request->validate(['song_id' => 'required|exists:songs,id']);
        $songId = $request->input('song_id');
        if (!$playlist->songs()->where('song_id', $songId)->exists()) {
            $order = $playlist->songs()->count();
            $playlist->songs()->attach($songId, ['order' => $order]);
        }
        return response()->json(['success' => true]);
    }

    public function removeSong(Playlist $playlist, Song $song)
    {
        $playlist->songs()->detach($song->id);
        return response()->json(['success' => true]);
    }

    public function getSongs(Playlist $playlist)
    {
        $songs = $playlist->songs()->get();
        return response()->json($songs);
    }
}