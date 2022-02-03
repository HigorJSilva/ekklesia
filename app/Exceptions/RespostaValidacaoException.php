<?php

namespace App\Exceptions;

use App\Helpers\Resposta;
use Illuminate\Contracts\Validation\Validator;
use Exception;

class RespostaValidacaoException extends Exception
{
    public function __construct(Validator $validator, string $message = null)
    {
        parent::__construct();
        $this->message = $message;
        $this->validator = $validator;
    }

    public function render()
    {
        return response()->json(new Resposta(false, $this->message, null, [$this->validator->errors()]));
    }
}
