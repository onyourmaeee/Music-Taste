<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaylistSongTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('playlist_song', function (Blueprint $table) {
        $table->id();
        $table->foreignId('playlist_id')->constrained()->onDelete('cascade');
        $table->foreignId('song_id')->constrained()->onDelete('cascade');
        $table->integer('order')->default(0);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('playlist_song');
}
}