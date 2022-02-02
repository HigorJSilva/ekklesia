<?php

namespace App\Exceptions;

use Exception;

class RespostaException extends Exception
{
    public $status;
    public $message;
    public $dados;
    public $erros;

    public function __construct($obj)
    {
        parent::__construct();
        $this->status = $obj->status ?? false;
        $this->message = $obj->message ?? $obj->mensagem ?? null;
        $this->dados = $obj->dados ?? null;
        $this->erros = $obj->erros ?? null;
    }
}
