<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Viewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ViewerRepository;
use App\Http\Resources\ViewerResourceCollection;
use Illuminate\Support\Facades\Storage;

class ViewerController extends Controller
{

    protected $viewerRepo;

    const ORDER_BY = array('name', 'id_movie', 'qty');
    const ORDER = array('asc', 'desc');

    public function __construct(ViewerRepository $viewerRepository)
    {
        $this->viewerRepo = $viewerRepository;
    }

    public function index()
    {
        $viewers = Viewer::all();
        return response([ 'viewers' => ViewerResource::collection($viewers), 'message' => 'Saved successfully'], 200);
    }

    public function list(Request $request)
    {
        $datas = (object)array();
        try {
            $page = (int) $request->page;
            $perPage = (int) $request->per_page < 1 ? 10 : $request->per_page;

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

            $response = $this->viewerRepo->viewerSection($rawData);

            $result = new ViewerResourceCollection($response);
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
            'name' => 'required',
            'id_movie' => 'required',
            'qty' => 'required'
        ]);
        
            try {
                    $rawData = [
                        'name' => $request['name'],
                        'id_movie' => $request['id_movie'],
                        'qty' => $request['qty']
                    ];

                    $this->viewerRepo->submit($rawData);

                    $result = ['message' => 'success', 'data' => $rawData];
                    return $this->returnJsonSuccess($result);

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
            'name' => 'required',
            'id_movie' => 'required',
            'qty' => 'required'
        ]);

            try {
                    $rawData = [
                        'name' => $request['name'],
                        'id_movie' => $request['id_movie'],
                        'qty' => $request['qty']
                    ];

                    $dataExisting= $this->viewerRepo->viewerById($request['id']);
                    if (empty($dataExisting)) {
                        $result = ['code' => 403, 'message' => 'Id section tidak valid.', 'data' => (object)[]];
                        return response()->json($result, 202);
                    }

                    $this->viewerRepo->action([$request['id']], $rawData);

                    $result = ['message' => 'success', 'data' => $rawData];
                    return $this->returnJsonSuccess($result);

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
