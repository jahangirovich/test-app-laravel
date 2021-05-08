<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {
        $loginData = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required'
        ]);

        if($loginData->fails()){

            $failedRules = $loginData->failed();

            if(isset($failedRules["email"])){
                return $this->handler("error", ["username"=>"Поле является обязательным для заполнения"], 400);
            }
            return $this->handler("error", ["password"=>"Поле является обязательным для заполнения"] ,400);
        }
        $credentials = [
            "email"      => $request["email"],
            "password"  => $request['password']
        ];

        if (!auth()->attempt($credentials)) {
            return $this->handler("error", "Неверный логин или пароль",400);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return $this->handler("ok", $accessToken);

    }

    public function handler($status,$message,$statusCode = 200){
        return response(["status" => $status, "message" => $message], $statusCode);
    }
}
