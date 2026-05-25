<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
{
    // Auto-populate genres table mula sa distinct song genres ng user
    $songGenres = \App\Models\Song::where('user_id', auth()->id())
        ->whereNotNull('genre')
        ->where('genre', '!=', '')
        ->distinct()
        ->pluck('genre');

    foreach ($songGenres as $genreName) {
        \App\Models\Genre::firstOrCreate(['name' => $genreName]);
    }

    $genres  = Genre::orderBy('name')->get();
    $featured = $genres->first();
    return view('genres.index', compact('genres', 'featured'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:genres,name',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('genres', 'public');
        }

        Genre::create([
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        return back()->with('success', 'Genre added!');
    }

    public function update(Request $request, Genre $genre)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:genres,name,' . $genre->id,
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $genre->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('genres', 'public');
        }

        $genre->update([
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        return back()->with('success', 'Genre updated!');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return back()->with('success', 'Genre deleted!');
    }
}