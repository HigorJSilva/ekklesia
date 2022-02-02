<?php

namespace App\Helpers;

class Resposta
{
    public function __construct(bool $status, ?string $mensagem, ?array $dados, ?array $erros)
    {
        $this->status = $status;
        $this->mensagem = $mensagem;
        $this->dados = $dados;
        $this->erros = $erros;
    }
    public static function object(bool $status, ?string $mensagem, ?array $dados, ?array $erros): Resposta
    {
        return new Resposta($status, $mensagem, $dados, $erros);
    }
    public static function array(bool $status, ?string $mensagem, ?array $dados, ?array $erros): array
    {
        return array('status' => $status, 'mensagem' => $mensagem, 'dados' => $dados, 'erros' => $erros);
    }
}
