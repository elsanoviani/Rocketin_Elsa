<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Viewer extends Model
{

    protected $table = 'viewer';
    protected $fillable = [
        'name',
        'id_movie',
        'qty'
    ];

}
