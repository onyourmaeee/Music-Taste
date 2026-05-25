<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['user_id', 'name', 'title', 'description', 'cover_image'];

    protected $appends = ['name', 'coverPhotoUrl'];

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_song')
                    ->withPivot('order')
                    ->orderBy('playlist_song.order');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->title;
    }

    public function getCoverPhotoUrlAttribute()
    {
        if ($this->cover_image) {
            if (str_starts_with($this->cover_image, 'http')) {
                return $this->cover_image;
            }
            return asset('storage/' . $this->cover_image);
        }
        return 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=300&auto=format&fit=crop';
    }
}