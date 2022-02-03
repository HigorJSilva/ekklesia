<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Teste extends BaseModel
{
    protected $table = 'teste';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'nome',
        'idade'
    ];
}
