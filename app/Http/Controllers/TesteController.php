<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\TesteRequest;
use App\Models\Teste;
use Illuminate\Support\Facades\Auth;

/**
 * Classes do Api resource estão em BaseController.
 * Adicione novas requisições conforme o exmplo
 *
 * 
 */
class TesteController extends BaseController
{

    protected $request = TesteRequest::class;
    /**
     * Prepara o request com o id do usuário logado.
     *
     * @return void
     */

    protected function prepareData($data)
    {
        $data['userId'] = Auth::user()->id;
        return $data;
    }

    /**
     * Exemplo de nova requisicao
     *
     * @return \Illuminate\Http\Response
     */
    //public function importar()
    //{
    //return jsonDefaultResponse($this->service->importarXls(request()->file('file')));
    //}
}
