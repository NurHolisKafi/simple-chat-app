<?php

namespace App\Http\Controllers;

use App\Events\MyApp;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Message;
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

    function sendMessage(Request $request)
    {
        broadcast(new MyApp($request->all()))->toOthers();
        Message::create([
            'id_user1' => $request->user1,
            'id_user2' => $request->user2,
            'message' => $request->message,
            'created_at' => $request->time,
            'updated_at' => $request->time
        ]);
        return response(['success' => 'Message Send Succesfully']);
    }

    function getMessages(Request $request, $from)
    {
        $messages =
            Message::where(function ($query) use ($request, $from) {
                $query->where('id_user1', $request->user()->id)
                    ->where('id_user2', $from);
            })->orWhere(function ($query) use ($request, $from) {
                $query->where('id_user1', $from)
                    ->where('id_user2', $request->user()->id);
            })->select('id_user2 as user1', 'id_user2 as user2', 'message', 'created_at as time')->orderBy('created_at')->get();
        $newFormat = $messages->map(function ($data) {
            return $data;
        });
        return response($newFormat);
    }
}
