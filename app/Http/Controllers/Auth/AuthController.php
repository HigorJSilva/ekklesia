<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $userCreds = $request->validate([
            'name' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'string',
                'unique:users'
            ],
            'password' => [
                'required',
                'string'
            ],
            'roleId' => [
                'exists:roles,id'
            ]
        ]);

        $user = User::create([
            'name' => $userCreds['name'],
            'email' => $userCreds['email'],
            'password' => bcrypt($userCreds['password']),
            'roleId' => $userCreds['roleId'],
        ]);

        $token = $user->createToken('token')->plainTextToken;
        return response(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'logout',
        ];
    }
}
