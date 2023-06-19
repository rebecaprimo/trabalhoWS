<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Resources\Api\ReservaResource;
use Illuminate\Validation\ValidationException;

class ReservaController extends Controller
{

    public function index($idHotel, Request $request)
    {
        try {

            $reservas = Reserva::where('idHotel', $idHotel);
            $mensagem = __("reserva.listreturn");
            $codigoderetorno = 200;

            $filterParameter = $request->input("filtro");
            if ($filterParameter == null) {
                $mensagem = $mensagem;
                $codigoderetorno = 200;
            } else {
                [$filterCriteria, $filterValue] = explode(":", $filterParameter);

                if ($filterCriteria == "idReserva") {
                    $reservas = $reservas->where("idReserva", "=", $filterValue);
                    $mensagem = $mensagem . "Filtrada";
                    $codigoderetorno = 200;
                } else {
                    $reservas = [];
                    $mensagem = "Filtro não aceito";
                    $codigoderetorno = 406;
                }
            }

            if ($codigoderetorno == 200) {
                if ($request->has('ordenacao')) {
                    $sorts = explode(',', $request->input('ordenacao'));

                    foreach ($sorts as $sortColumn) {
                        $sortDirection = Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
                        $sortColumn = ltrim($sortColumn, '-');

                        switch ($sortColumn) {
                            case "idReserva":
                                $reservas = $reservas->orderBy('idReserva', $sortDirection);
                                break;
                            case "idCliente":
                                $reservas = $reservas->orderBy('idCliente', $sortDirection);
                                break;
                            case "idHotel":
                                $reservas = $reservas->orderBy('idHotel', $sortDirection);
                                break;
                        }
                    }
                    $mensagem = $mensagem . "+Ordenada";
                }
            }


            $input = $request->input('pagina');
            if ($input) {
                $page = $input;
                $perPage = 10;
                $reservas->offset(($page - 1) * $perPage)->limit($perPage);

                $recordsTotal = Reserva::count();
                $numberofPages = ceil($recordsTotal / $perPage);
                $mensagem = $mensagem . "+Paginada";
            }

            if ($codigoderetorno == 200) {
                $reservas = $reservas->get();
                $response = response()->json([
                    'status' => 200,
                    'mensagem' =>  $mensagem,
                    'hoteis' => ReservaResource::collection($reservas)
                ], 200);
            } else {
                $response = response()->json([
                    'status' => 406,
                    'mensagem' => $mensagem,
                    'reservas' => $reservas
                ], 406);
            }

            return $response;
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
                'mensagem' => __("reserva.created"),
                'reservas' => $reserva
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar a reserva'], 500);
        }
    }

    public function show($idHotel, $idReserva)
    {
        try {
            $validator = Validator::make(['idHotel' => $idHotel], [
                'idHotel' => 'integer'
            ]);
            
            if ($validator->fails()) {
                throw ValidationException::withMessages(['idHotel' => 'O campo Id deve ser numérico']);
            }
      
            $reserva = Reserva::findOrFail($idReserva);

            return response()->json([
                'status' => 200,
                'mensagem' => __("reserva.returned"),
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
                'mensagem' => __("reserva.updated"),
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

            return response()->json([
                'status' => 200,
                'mensagem' => __("reserva.deleted")
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir a reserva'], 500);
        }
    }
}
