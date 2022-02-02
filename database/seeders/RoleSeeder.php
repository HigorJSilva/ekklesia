<?php

namespace Database\Seeders;


class RoleSeeder extends BaseSeeder
{
    protected $table = 'roles';
    protected $data = [
        ['name' => 'Administrator'],
        ['name' => 'Condomino'],
        ['name' => 'Morador']
    ];
}
