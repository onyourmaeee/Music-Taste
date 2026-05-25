<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = ['user_id', 'title', 'artist', 'genre', 'album', 'youtube_url', 'thumbnail', 'audio_url'];

    protected $appends = ['youtube_id', 'thumbnail_url', 'audio_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_song');
    }

    public function getYoutubeIdAttribute()
    {
        $url = $this->youtube_url;
        if (!$url) return null;
        if (preg_match('/youtube\.com\/watch\?(?:.*&)?v=([a-zA-Z0-9_-]{11})/', $url, $m)) return $m[1];
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m)) return $m[1];
        if (preg_match('/youtube\.com\/(?:embed|v|shorts|live)\/([a-zA-Z0-9_-]{11})/', $url, $m)) return $m[1];
        return null;
    }

    public function getThumbnailUrlAttribute()
    {
        $id = $this->youtube_id;
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    public function getAudioUrlAttribute()
    {
        return $this->audio_url ?? $this->youtube_url;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($song) {
            $song->playlists()->detach();
        });
    }
}