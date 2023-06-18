<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Viewer;
use Carbon\Carbon;

class ViewerRepository
{
    
    public static function viewerSection($rawData)
    {
        $data = \DB::table('movie.viewer')
        ->join('movie.movie_projects', 'viewer.id_movie', '=', 'movie_projects.id')
        ->select('viewer.name','viewer.id_movie','viewer.qty','movie_projects.title','movie_projects.description','movie_projects.duration','movie_projects.artists',
        'movie_projects.genres');

            $filter = $rawData['filter'] ? $rawData['filter'] : '';
            if($filter != '') {
                $data = $data->where(function($subquery) use($filter){
                    $subquery->whereRaw('LOWER(name) like "%'.$filter.'%"')
                            ->orWhereRaw('LOWER(id_movie) like "%'.$filter.'%"')
                            ->orWhereRaw('LOWER(qty) like "%'.$filter.'%"');
                });
            }

            if (strlen($rawData['order_by']) > 0) {
                $data = $data->orderBy($rawData['order_by'], $rawData['order']);
             } else {
                $data = $data->orderBy('viewer.created_at','desc');
             }

        $datas = $data->paginate($rawData['per_page']);

        return $datas;
    }

    public static function submit($rawData)
    {
        Viewer::create($rawData);
    }

    public static function viewerById($id)
    {
        $datas = Viewer::selectRaw('name,id_movie,qty')
            ->where('id', $id)->first();

        return $datas;
    }

    public static function action($id, $rawData)
    {
        Viewer::whereIn('id', $id)->update($rawData);
    }
}
