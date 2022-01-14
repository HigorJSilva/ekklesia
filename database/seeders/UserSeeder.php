<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return User::create([
            'name' => 'admin',
            'password' => Hash::make('123'),
            'email' => 'admin@gmail.com',
            'roleId' => 1,
        ]);
        return User::create([
            'name' => 'condomino',
            'password' => Hash::make('123'),
            'email' => 'condomino@gmail.com',
            'roleId' => 2,
        ]);
        return User::create([
            'name' => 'morador',
            'password' => Hash::make('123'),
            'email' => 'morador@gmail.com',
            'roleId' => 2,
        ]);
    }
}
