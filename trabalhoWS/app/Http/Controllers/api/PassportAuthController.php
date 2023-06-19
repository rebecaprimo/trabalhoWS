<?php

namespace App\Http\Controllers\api;



use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomeCliente' => 'required',
            'emailCliente' => 'required|email|unique:clientes,emailCliente',
            'senha' => 'required|min:4',
            'cpfCliente' => 'required|min:11',
            'telefoneCliente'=> 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 500);
        }

        $cliente = Cliente::create([
            'nomeCliente' => $request->nomeCliente,
            'emailCliente' => $request->emailCliente,
            'telefoneCliente' => $request->telefoneCliente,
            'cpfCliente' => $request->cpfCliente,
            'senha' => bcrypt($request->senha),
        ]);

        $token = $cliente->createToken('Laravel-9-Passport-Auth')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {

        $data = [
            'emailCliente' => $request->emailCliente,
            'password' => $request->senha,
        ];

        if (auth()->attempt($data)) {

            $token = auth()->user()->createToken('Laravel-9-Passport-Auth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

    }

    public function logout(Request $request)
    {
        $accessToken = Auth::user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();

        return response([
            'message' => 'You have been successfully logged out.',
        ], 200);
    }

    public function clienteInfo()
    {
        $cliente = Auth::user();

        return response()->json(['cliente' => $cliente], 200);
    }
}
