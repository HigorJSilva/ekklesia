<?php

namespace App\Helpers;

use App\Exceptions\RespostaException;
use App\Http\Controllers\Controller;
use App\Services\CrudService;
use Exception;
use Illuminate\Http\Request;

abstract class JsonCrudController extends Controller
{
    /**
     * @var CrudService
     */
    protected $service;

    public function __construct()
    {
        $resourceName = str_replace('Controller', '', class_basename($this));
        $this->service = app('App\Services\\' . $resourceName . 'Service');
    }

    private function getRequest($request)
    {
        return method_exists($request, 'validated') && is_callable([$request, 'validated']) ? $request->validated() : $request->all();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json($this->service->list($this->prepareFilters($this->getRequest($request))));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json($this->service->save($this->prepareData($this->getRequest($request))));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        return response()->json($this->service->search($id, $this->prepareFilters($this->getRequest($request))));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response()->json($this->service->update($id, $this->prepareData($this->getRequest($request))));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->service->deactivate($id);
        } catch (Exception $e) {
            return response()->json(new RespostaException(false, $e->getMessage(), null, null));
        }
        return response()->json((object) $this->service->deactivate($id));
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reactivate($id)
    {
        return response()->json((object) $this->service->reactivate($id));
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareFilters($data)
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        return $data;
    }
}
