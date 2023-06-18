<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;


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

    public function login(Request $request)
    {
        $credentials = [
            'emailCliente' => $request->emailCliente,
            'senha' => $request->senha,
        ];
        
        if (auth()->attempt($credentials)) {
            $token = $request->user()->createToken('Laravel-9-Passport-Auth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
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
