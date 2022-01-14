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
            'name' => 'role1',
            'password' => Hash::make('123'),
            'email' => 'role1@gmail.com',
            'roleId' => 2,
        ]);
        return User::create([
            'name' => 'role2',
            'password' => Hash::make('123'),
            'email' => 'role2@gmail.com',
            'roleId' => 2,
        ]);
    }
}
