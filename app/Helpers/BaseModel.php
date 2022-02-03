<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    protected $hidden = [
        'dataCadastro',
        'dataEdicao',
    ];
    public $guardFromUpdate = [
        'id',
        'dataCadastro',
        'dataEdicao',
    ];
    public static $snakeAttributes = false;
    public $queryFilters = [];
}
