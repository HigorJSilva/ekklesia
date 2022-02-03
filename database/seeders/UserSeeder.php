<?php

namespace Database\Seeders;


class UserSeeder extends BaseSeeder
{
    protected $table = 'users';
    protected $data = [
        [
            'name' => 'admin',
            'password' => '$2y$10$uaYP7hQ4kbJJQNhr.3/A5ei91igbte324r4XYHdoidLczy85iPzBO',
            'email' => 'admin@gmail.com',
            'roleId' => 1,
        ],
        [
            'name' => 'role1',
            'password' => '$2y$10$uaYP7hQ4kbJJQNhr.3/A5ei91igbte324r4XYHdoidLczy85iPzBO',
            'email' => 'role1@gmail.com',
            'roleId' => 2,
        ],
        [
            'name' => 'role2',
            'password' => '$2y$10$uaYP7hQ4kbJJQNhr.3/A5ei91igbte324r4XYHdoidLczy85iPzBO',
            'email' => 'role2@gmail.com',
            'roleId' => 2,
        ],
    ];
}
