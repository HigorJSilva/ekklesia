<?php

namespace App\Http\Requests;


class TesteRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'nome' => [
                'required',
            ],
            'idade' => [
                'required',
            ]
        ];
    }
}
