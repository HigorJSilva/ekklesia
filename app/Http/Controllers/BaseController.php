<?php

namespace App\Http\Controllers;

use App\Helpers\JsonCrudController;
use Illuminate\Http\Request;

class BaseController extends JsonCrudController
{
    public function __invoke()
    {
    }

    public function getService()
    {
        return $this->service;
    }

    public function store(Request $request)
    {
        return parent::store($this->formRequest($request));
    }

    public function update(Request $request, $id)
    {
        return parent::update($this->formRequest($request), $id);
    }

    protected function formRequest($request)
    {
        return isset($this->request) ? app($this->request) : $request;
    }

    protected function prepareFilters($data)
    {
        return $data;
    }
}
