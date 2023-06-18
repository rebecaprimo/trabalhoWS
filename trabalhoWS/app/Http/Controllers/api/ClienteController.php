<?php

namespace App\Http\Controllers\api;

use App\Models\Cliente;
use App\Http\Resources\ClienteResource;
use App\Http\Requests\StoreClienteRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Cliente::query();
        $mensagem = __("cliente.listreturn");
        $codigoderetorno = 0;

        $filterParameter = $request->input("filtro");
        if ($filterParameter == null) {
            $mensagem = $mensagem . "+completa";
            $codigoderetorno = 200;
        } else {
            [$filterCriteria, $filterValue] = explode(":", $filterParameter);

            if ($filterCriteria == "nomeCliente") {
                $cliente = $cliente->where("nomeCliente", "=", $filterValue);
                $mensagem = $mensagem . "+Filtrada";
                $codigoderetorno = 200;
            } else {
                $cliente = [];
                $mensagem = "Filtro não aceito";
                $codigoderetorno = 406;
            }
        }

        if ($codigoderetorno == 200) {
            if ($request->input('ordenacao')) {
                $sorts = explode(',', $request->input('ordenacao'));

                foreach ($sorts as $sortColumn) {
                    $sortDirection = Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
                    $sortColumn = ltrim($sortColumn, '-');

                    switch ($sortColumn) {
                        case "idCliente":
                            $cliente->orderBy('idCliente', $sortDirection);
                            break;
                        case "nomeCliente":
                            $cliente->orderBy('nomeCliente', $sortDirection);
                            break;
                        case "cpfCliente":
                            $cliente->orderBy('cpfCliente', $sortDirection);
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
            $cliente->offset(($page - 1) * $perPage)->limit($perPage);

            $recordsTotal = Cliente::count();
            $numberofPages = ceil($recordsTotal / $perPage);
            $mensagem = $mensagem . "+Paginada";
        }

        if ($codigoderetorno == 200) {
            $clientes = $cliente->get();
            $response = response()->json([
                'status' => 200,
                'mensagem' =>  __("cliente.listreturn"),
                'hoteis' => ClienteResource::collection($clientes)
            ], 200);
        } else {
            $response = response()->json([
                'status' => 406,
                'mensagem' => $mensagem,
                'cliente' => $cliente
            ], 406);
        }

        return $response;
    }

    public function show($idcliente)
    {
        try {

            $validator = Validator::make(
                ['id' => $idcliente],
                [
                    'id' => 'integer'
                ]
            );

            if ($validator->fails()) {
                throw ValidationException::withMessages(['id' => 'O campo Id deve ser numério']);
            }

            $cliente = Cliente::findorfail($idcliente);

            return response()->json([
                'status' => 200,
                'mensagem' => __("cliente.returned"),
                'cliente' => new ClienteResource($cliente),
            ]);
        } catch (\Exception $ex) {
            $class = get_class($ex); //Pega a classe da exceção
            switch ($class) {
                case ModelNotFoundException::class: // Caso não exista o id na base
                    return response()->json([
                        'status' => 404,
                        'mensagem' => 'Cliente não encontrado :(',
                        'cliente' => []
                    ], 400);
                    break;
                case \Illuminate\Validation\ValidationException::class: //Caso seja erro de validação
                    return response()->json([
                        'status' => 406,
                        'mensagem' => $ex->getMessage(),
                        'cliente' => []
                    ], 406);
                    break;
                default: // Algum erro interno ocorreu
                    return response()->json([
                        'status' => 500,
                        'mensagem' => 'Erro Interno! x ~ x',
                        'cliente' => []
                    ], 500);
                    break;
            }
        }
    }

    public function update(StoreClienteRequest $request, Cliente $cliente)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'telefone' => 'required|min:8',
            'cpf' => 'required|min:11',
        ]);

        $cliente = Cliente::find($cliente->idCliente);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $cliente->nomeCliente = $request->name;
        $cliente->emailCliente = $request->email;
        $cliente->telefoneCliente = $request->telefone;
        $cliente->cpfCliente = $request->cpf;

        $cliente->update();

        return response()->json([
            'status' => 200,
            'mensagem' => __("cliente.updated"),
        ], 200);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente = Cliente::find($cliente->idCliente);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $cliente->delete();

        return response()->json([
            'status' => 200,
            'message' => __("cliente.deleted")
        ], 200);
    }
}
