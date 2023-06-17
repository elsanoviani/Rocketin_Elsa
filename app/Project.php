<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $table = 'movie_projects';
    protected $fillable = [
        'title',
        'description',
        'duration',
        'artists',
        'genres',
        'watch_url'
    ];

}
