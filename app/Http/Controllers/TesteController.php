<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\TesteRequest;
use App\Services\TesteService;

class TesteController extends BaseController
{
    protected $service;
    protected $request = TesteRequest::class;

    public function __construct(TesteService $service)
    {
        $this->service = $service;
    }
}
