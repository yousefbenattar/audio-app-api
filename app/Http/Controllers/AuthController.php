<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attrs = $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:5|confirmed"
        ]);
        $user = User::create([
            "name" => $attrs['name'],
            "email" => $attrs['email'],
            "password" => bcrypt($attrs["password"])
        ]);
        return response([
            "user" => $user,
            "token" => $user->createToken('secret')->plainTextToken
        ], 200);
    }

    public function login(Request $request)
    {
        $attrs = $request->validate([
            "email" => "required|email",
            "password" => "required|min:5"
        ]);
        if (!Auth::attempt($attrs)) {
            return response([
                "message" => "invalid credentials"
            ], 403);
        }
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    public  function update (Request $request){
        $attrs = $request->validate(["name" => "required|string"]);
        auth()->user()->update(["name" => $attrs["name"]]);
        return response([
            "message" => "user updated",
            "user" => auth()->user()
        ],200);
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return response ([
            "message" => "logout success"
        ]);
    }

    public function getuser (){
        return response ([
            "message" => auth()->user()
        ]);
    }
}
