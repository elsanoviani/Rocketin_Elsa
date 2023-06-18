<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\MovieRepository;
use App\Http\Resources\MovieResourceCollection;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{

    protected $movieRepo;

    const ORDER_BY = array('title', 'artists', 'genres', 'description', 'duration', 'watch_url');
    const ORDER = array('asc', 'desc');

    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepo = $movieRepository;
    }

    public function index()
    {
        $projects = Project::all();
        return response([ 'projects' => MovieResource::collection($projects), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'cost' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $project = Project::create($data);

        return response(['project' => new MovieResource($project), 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return response(['project' => new MovieResource($project), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function updateData(Request $request, Project $project)
    {
        $project->update($request->all());

        return response(['project' => new MovieResource($project), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response(['message' => 'Deleted']);
    }

    public function list(Request $request)
    {
        $datas = (object)array();
        try {
            $page = (int) $request->page;
            $perPage = (int) $request->per_page < 1 ? 10 : $request->per_page;

            $status = $request->status ?? '';
            $orderBy = $request->order_by ?? '';
            $filter = $request->filter ?? '';
            $order = $request->order ?? '';
            $orderBy = (!($this->orderByListSection($orderBy, $order))) ? "" : $orderBy;

            $rawData = [
                'page' => $page,
                'per_page' => $perPage,
                'filter' => $filter,
                'order_by' => $orderBy,
                'order' => $order
            ];

            $response = $this->movieRepo->movieSection($rawData);

            $result = new MovieResourceCollection($response);
            return $this->returnJsonSuccess($result->toArray($request));
        } catch (\Exception $e) {
            $result = [
                'code'      => 400,
                'message'   => $e->getMessage(),
                'data'      => $datas
            ];
        }
        return response()->json($result, 202);
    }

    public function submit(Request $request)
    {
        $hasError = $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'duration' => 'required',
            'artists' => 'required',
            'genres' => 'required',
            'watch_url' => 'required|file|mimes:png'
        ]);
        
            try {
                $fileName = $request->watch_url->getClientOriginalName();
                $filePath = 'videos/' . $fileName;
        
                $isFileUploaded = Storage::disk('public')->put($filePath, file_get_contents($request->watch_url));
        
                // File URL to access the video in frontend
                $url = Storage::disk('public')->url($filePath);
        
                if ($isFileUploaded) {
        
                    $rawData = [
                        'title' => $request['title'],
                        'description' => $request['description'],
                        'duration' => $request['duration'],
                        'artists' => $request['artists'],
                        'genres' => $request['genres'],
                        'watch_url' => $filePath
                    ];

                    $this->movieRepo->submit($rawData);

                    $result = ['message' => 'success', 'data' => $rawData];
                    return $this->returnJsonSuccess($result);

                }
            } catch (\Exception $e) {
                $result = [
                    'code'      => 400,
                    'message'   => $e->getMessage(),
                    'data'      => (object)[]
                ];
            }
        
        return response()->json($result, 202);
    }

    public function update(Request $request)
    {
        $hasError = $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'duration' => 'required',
            'artists' => 'required',
            'genres' => 'required',
            'watch_url' => 'required|file|mimes:png'
        ]);

            try {
                $fileName = $request->watch_url->getClientOriginalName();
                $filePath = 'videos/' . $fileName;
        
                $isFileUploaded = Storage::disk('public')->put($filePath, file_get_contents($request->watch_url));
        
                // File URL to access the video in frontend
                $url = Storage::disk('public')->url($filePath);
        
                if ($isFileUploaded) {
        
                    $rawData = [
                        'title' => $request['title'],
                        'description' => $request['description'],
                        'duration' => $request['duration'],
                        'artists' => $request['artists'],
                        'genres' => $request['genres'],
                        'watch_url' => $filePath
                    ];

                    $dataExisting= $this->movieRepo->movieById($request['id']);
                    if (empty($dataExisting)) {
                        $result = ['code' => 403, 'message' => 'Id section tidak valid.', 'data' => (object)[]];
                        return response()->json($result, 202);
                    }

                    $this->movieRepo->action([$request['id']], $rawData);

                    $result = ['message' => 'success', 'data' => $rawData];
                    return $this->returnJsonSuccess($result);

                }
            } catch (\Exception $e) {
                $result = [
                    'code'      => 400,
                    'message'   => $e->getMessage(),
                    'data'      => (object)[]
                ];
            }
        
        return response()->json($result, 202);
    }

    private function orderByListSection($orderBy, $order)
    {
        $orderBy = strtolower($orderBy);
        $order = strtolower($order);

        $result = ((in_array($orderBy, self::ORDER_BY)) && (in_array($order, self::ORDER))) ? true : false;

        return $result;
    }

    protected function returnJsonSuccess($msg = [])
    {
        $result = [
            'success' => true,
            'code' => 200
        ];
        $result = array_merge($result, $msg);
        return response()->json($result);
    }
    

}
