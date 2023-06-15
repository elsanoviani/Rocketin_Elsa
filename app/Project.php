<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $fillable = [
        'title',
        'description',
        'duration',
        'artists',
        'genres',
        'watch_url'
    ];

}
