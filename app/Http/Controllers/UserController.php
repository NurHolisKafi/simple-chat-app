<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function listPerson(Request $request)
    {
        return UserResource::collection(User::whereNot('id', $request->user()->id)->get());
    }


    function authlogin(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('name', $data['username'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response([
                "message" => "User Not Found"
            ], 404);
        }

        return response([
            "data" => $user,
            "token" => $user->createToken($user->name)->plainTextToken,
        ]);
    }

    function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'message' => 'User has been logout'
        ], 200);
    }
}
