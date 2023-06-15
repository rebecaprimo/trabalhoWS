<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return response()->json($clientes);
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        return response()->json($cliente);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomeCliente' => 'required',
            'emailCliente' => 'required|email|unique:clientes,emailCliente',
            'telefoneCliente' => 'required',
            'cpfCliente' => 'required|unique:clientes,cpfCliente',
        ]);

        $cliente = Cliente::create($request->all());
        return response()->json($cliente, 201);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $request->validate([
            'nomeCliente' => 'required',
            'emailCliente' => 'required|email|unique:clientes,emailCliente,' . $id . ',idCliente',
            'telefoneCliente' => 'required',
            'cpfCliente' => 'required|unique:clientes,cpfCliente,' . $id . ',idCliente',
        ]);

        $cliente->update($request->all());
        return response()->json($cliente);
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $cliente->delete();
        return response()->json(['message' => 'Cliente removido com sucesso']);
    }
}
