<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservaController extends Controller
{

    public function index($idHotel)
    {
        try {
            $reservas = Reserva::where('idHotel', $idHotel)->get();

            return response()->json([
                'status' => 200,
                'mensagem' => 'Lista de reservas retornada',
                'reservas' => $reservas
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao recuperar as reservas'], 500);
        }
    }

    public function store(Request $request, $idHotel)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idCliente' => 'required|exists:clientes,idCliente',
                'dataInicio' => 'required|date',
                'dataFim' => 'required|date',
                'numHospedes' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $reserva = new Reserva;
            $reserva->idCliente = $request->idCliente;
            $reserva->dataInicio = $request->dataInicio;
            $reserva->dataFim = $request->dataFim;
            $reserva->numHospedes = $request->numHospedes;
            $reserva->idHotel = $idHotel;
            $reserva->save();

            return response()->json([
                'status' => 201,
                'mensagem' => 'Reserva efetuada com sucesso',
                'reservas' => $reserva
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar a reserva'], 500);
        }
    }

    public function show($idHotel, $idReserva)
    {
        try {
            $reserva = Reserva::where('idHotel', $idHotel)->findOrFail($idReserva);

            return response()->json([
                'status' => 200,
                'mensagem' => 'Dados da reserva',
                'reservas' => $reserva
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Reserva não encontrada'], 404);
        }
    }

    public function update(Request $request, $idHotel, $idReserva)
    {
        try {
            $validator = Validator::make($request->all(), [
                'idHotel' => 'exists:hoteis,idHotel',
                'idCliente' => 'exists:clientes,idCliente',
                'dataInicio' => 'date',
                'dataFim' => 'date',
                'numHospedes' => 'integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $reserva = Reserva::where('idHotel', $idHotel)->findOrFail($idReserva);
            $reserva->update($request->all());

            return response()->json([
                'status' => 200,
                'mensagem' => 'Reserva atualizada com sucesso',
                'reservas' => $reserva
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar a reserva'], 500);
        }
    }

    public function destroy($idHotel, $idReserva)
    {
        try {
            $reserva = Reserva::where('idHotel', $idHotel)->findOrFail($idReserva);
            $reserva->delete();
    
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir a reserva'], 500);
        }
    }
}
