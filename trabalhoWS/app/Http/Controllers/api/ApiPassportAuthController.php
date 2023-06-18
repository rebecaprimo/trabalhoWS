<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class ApiPassportAuthController extends Controller
{

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'telefone' => 'required|min:8',
            'cpf' => 'required|min:11',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'nomeCliente' => $request->name,
            'emailCliente' => $request->email,
            'telefoneCliente' => $request->telefone,
            'cpfCliente' => $request->cpf,
            'senha' => bcrypt($request->password)
        ]);

        $token = $user->createToken('Laravel-9-Passport-Auth')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request){
        $data  = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        
        dd(Auth::guard('api')->once($data));
        if (Auth::guard('api')->once($data)) {
            $token = $request->user()->createToken('Laravel-9-Passport-Auth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();

        return response([
            'message' => 'You have been successfully logged out. ',
        ], 200);
    }

    public function userInfo()
    {
        $user = auth()->user();

        return response()->json(['user' => $user], 200);
    }
}
