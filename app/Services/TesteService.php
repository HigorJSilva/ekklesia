<?php

namespace App\Services;

use App\Models\Teste;

class TesteService extends CrudService
{

    public function getModel($data = [])
    {
        return new Teste($data);
    }
}
