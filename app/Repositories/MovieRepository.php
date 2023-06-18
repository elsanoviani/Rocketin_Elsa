<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Project;
use Carbon\Carbon;

class MovieRepository
{
    
    public static function movieSection($rawData)
    {
        $data = \DB::table('movie.movie_projects')
            ->select('movie_projects.title','movie_projects.description','movie_projects.duration','movie_projects.artists',
            'movie_projects.genres','movie_projects.watch_url','movie_projects.created_at');

            $filter = $rawData['filter'] ? $rawData['filter'] : '';
            if($filter != '') {
                $data = $data->where(function($subquery) use($filter){
                    $subquery->whereRaw('LOWER(title) like "%'.$filter.'%"')
                            ->orWhereRaw('LOWER(description) like "%'.$filter.'%"')
                            ->orWhereRaw('LOWER(artists) like "%'.$filter.'%"')
                            ->orWhereRaw('LOWER(genres) like "%'.$filter.'%"');
                });
            }

            if (strlen($rawData['order_by']) > 0) {
                $data = $data->orderBy($rawData['order_by'], $rawData['order']);
             } else {
                $data = $data->orderBy('movie_projects.created_at','desc');
             }

        $datas = $data->paginate($rawData['per_page']);

        return $datas;
    }

    public static function submit($rawData)
    {
        Project::create($rawData);
    }

    public static function movieById($id)
    {
        $datas = Project::selectRaw('title,description,duration,artists,genres,watch_url')
            ->where('id', $id)->first();

        return $datas;
    }

    public static function action($id, $rawData)
    {
        Project::whereIn('id', $id)->update($rawData);
    }
}
