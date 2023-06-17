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
        /**
         * $query = Reservas::with('cliente', 'hoteis');
         * $mensagem = "Lista de reservas retornada";
         * $codigoretorno = 0;
         *
         * //OBTEM PARAMETRO DO FILTRO
         * $filterParameter = $request -> input("filtro");
         *
         * //$f nao ha um parametro;
         * if($filterParameter == null) {
         *      //Retorna todos as reservas & Default
         *      $mensagem = "Lista de reservas retornada - Completa";
         *      $codigoretorno = 200;
         * } else {
         *      //Obtem o nome do filtro e o criteiro
         *      [$filterCriteria, $filterValue] = explode(":", $filterParameter);
         *
         *      //Se o filtro está adequado
         *      if($filterCriteria == "nomeCliente") {
         *          //Faz inner join para obter o Cliente
         *          $reservas = $query->join("clientes", "idCliente", "=", "idCliente")
         *                                        ->where("nomeCliente", "=", $filterValue);
         *          $mensagem = "Lista de reservas retornada - Filtrada";
         *          $codigoderetorno = 200;
         *      } else {
         *          //Usuario chamou um filtro que não existe, então nao ha nada a retornadar (Error 406 - No Accepted)
         * '        $produtos = [ ];
         *          $mensagem = "Filtro nao aceito";
         *          $codigoretorno = 406;
         *      }
         * }
         *
         * if($codigoretorno == 200) {
         *      //Retorno o processamento da ordenacao
         *
         *      //se há input para ornedacao
         *
         *      if($request->input('ordenacao'), ' ')) {
         *          $sorts = explode (' , ' $request->input('ordenacao', ' '));
         *          foreach($sorts as $sortColumn) {
         *              $sortDirection = Str::startsWith($sortColumn, ' - ')? 'desc' : 'asc';
         *              $sortColumn = ltrim($sortColumn, ' - ');
         *
         *
         *              //Transforma os nomes dos parametros em nomes dos campos de Modelo
         *              switch($sortColumn) {
         *                   case("dataInicio");
         *                      $query->orderBy('dataInicio', $sortDirection);
         *                      break;
         *                  case("dataFim");
         *                      $query->orderBy('dataFim', $sortDirection);
         *                      break;
         *                  case("numHospedes");
         *                      $query->orderBy('numHospedes', $sortDirection);
         *                      break;
         *
         *                  }
         *              }
         *              $mensagem = $mensagem . "+Ordenada";
         *          }
         *      }
         * }
         *
         * $input = $request->input('pagina');
         * if($input) {
         *      $page = $input;
         *      $perPage = 10; //Registros por pagina
         *      $query->offset(($page-1) * $perPage)->limit($perPage);
         *      $reservas = $query->get( );
         *
         *      $recordsTotal = Produto::count( );
         *      $numberOfPages = ceil($recordsTotal / $perPage);
         *
         *      $mensagem = $mensagem . "+Paginada";
         * }
         *
         * //Processamento foi ok, reotrna com base no criterio
         * if($codigoretorno == 200) {
         *      $reservas = $query->get( );
         *      $response = response( ) -> json([
         *          'status' => 200,
         *          'mensagem' => $mensagem,
         *          'reservas' => ReservaResource::collection($reservas)
         *      ], 200);
         *
         *
         * } else {
         *      //Retorna o erro que ocorreu
         *      $response = response()->json([
         *         'status' => 406,
         *         'mensagem' => $mensagem,
         *         'prodtuos' => $produtos
         *      ], 406);
         * }
         *
         * return $response;
         *
         *
         */

        $sortParameter = $request->input('ordenacao', 'nomeCliente');
        $sortDirection = Str::startsWith($sortParameter, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortParameter, '-');

        if ($sortColumn == 'nomeCliente') {
            $clientes = Cliente::orderBy('nomeCliente', $sortDirection)->get();
        } else{
            $clientes = Cliente::all();
        }
        return response( ) -> json ([
            'status' => 200,
            'mensagem' => __("cliente.listreturn"),
            'Clientes' => ClienteResource::collection($clientes),
        ], 200);
    }

    public function show($idcliente)
    {
        // $cliente = Cliente::find($id);

        // if (!$cliente) {
        //     return response()->json(['message' => 'Cliente não encontrado'], 404);
        // }

        // return response()->json($cliente);

        try {
            /**
             * Validação da entrada para ter certeza que o valor é numerico
             */

             $validator = Validator::make(['id' => $idcliente],
             [
                'id' => 'integer'
             ]);
             //Caso não seja válido, levantar exceção
             if($validator->fails()){
                throw ValidationException::withMessages(['id' => 'O campo Id deve ser numério']);
             }

             /*
                Continua o fluxo para execução
             */
            $cliente = Cliente::findorfail($idcliente);

            return response() -> json ([
                'status' => 200,
                'mensagem' => __("cliente.returned"),
                'cliente' => new ClienteResource($cliente),
            ]);
        } catch (\Exception $ex) {
            /**
             * Tratemento das exceções levantadas
             */

             $class = get_class($ex); //Pega a classe da exceção
            switch($class) {
                case ModelNotFoundException::class: // Caso não exista o id na base
                    return response ( ) -> json([
                        'status' => 404,
                        'mensagem' => 'Cliente não encontrado :(',
                        'cliente' => [ ]
                    ], 400);
                    break;
                case \Illuminate\Validation\ValidationException::class: //Caso seja erro de validação
                    return response( ) -> json ([
                        'status' => 406,
                        'mensagem' => $ex->getMessage(),
                        'cliente' => [ ]
                    ], 406);
                    break;
                default: // Algum erro interno ocorreu
                    return response() -> json ([
                        'status' => 500,
                        'mensagem' => 'Erro Interno! x ~ x',
                        'cliente' => [ ]
                    ], 500);
                    break;
            }
        }
    }

    public function store(StoreClienteRequest $request)
    {
        // $request->validate([
        //     'nomeCliente' => 'required',
        //     'emailCliente' => 'required|email|unique:clientes,emailCliente',
        //     'telefoneCliente' => 'required',
        //     'cpfCliente' => 'required|unique:clientes,cpfCliente',
        // ]);

        // $cliente = Cliente::create($request->all());
        // return response()->json($cliente, 201);

        $cliente = new Cliente();

        $cliente->nomeCliente = $request->nomeCliente;
        $cliente->emailCliente = $request->emailCliente;
        $cliente->telefoneCliente = $request->telefoneCliente;
        $cliente->cpfCliente = $request->cpfCliente;

        $cliente->save();

        return response() -> json([
            'status' => 200,
            'mensagem' => __("cliente.created"),
            'Cliente' => new ClienteResource($cliente),
        ], 200);
    }

    public function update(StoreClienteRequest $request, Cliente $cliente)
    {

        $cliente = Cliente::find($cliente->idCliente);


        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $cliente->nomeCliente = $request->nomeCliente;
        $cliente->emailCliente = $request->emailCliente;
        $cliente->telefoneCliente = $request->telefoneCliente;
        $cliente->cpfCliente = $request->cpfCliente;



        $cliente->update();

        return response() -> json([
            'status' => 200,
            'mensagem' => __("cliente.updated"),
        ], 200);

        // if (!$cliente) {
        //     return response()->json(['message' => 'Cliente não encontrado'], 404);
        // }

        // $request->validate([
        //     'nomeCliente' => 'required',
        //     'emailCliente' => 'required|email|unique:clientes,emailCliente,' . $id . ',idCliente',
        //     'telefoneCliente' => 'required',
        //     'cpfCliente' => 'required|unique:clientes,cpfCliente,' . $id . ',idCliente',
        // ]);

        // $cliente->update($request->all());
        // return response()->json($cliente);
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



// class ClienteController extends BaseController
// {
//     public function index()
//     {
//         $clientes = Cliente::all();
//         return response()->json($clientes);
//     }

//     public function show($id)
//     {
//         $cliente = Cliente::find($id);

//         if (!$cliente) {
//             return response()->json(['message' => 'Cliente não encontrado'], 404);
//         }

//         return response()->json($cliente);
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'nomeCliente' => 'required',
//             'emailCliente' => 'required|email|unique:clientes,emailCliente',
//             'telefoneCliente' => 'required',
//             'cpfCliente' => 'required|unique:clientes,cpfCliente',
//         ]);

//         $cliente = Cliente::create($request->all());
//         return response()->json($cliente, 201);
//     }

//     public function update(Request $request, $id)
//     {
//         $cliente = Cliente::find($id);

//         if (!$cliente) {
//             return response()->json(['message' => 'Cliente não encontrado'], 404);
//         }

//         $request->validate([
//             'nomeCliente' => 'required',
//             'emailCliente' => 'required|email|unique:clientes,emailCliente,' . $id . ',idCliente',
//             'telefoneCliente' => 'required',
//             'cpfCliente' => 'required|unique:clientes,cpfCliente,' . $id . ',idCliente',
//         ]);

//         $cliente->update($request->all());
//         return response()->json($cliente);
//     }

//     public function destroy($id)
//     {
//         $cliente = Cliente::find($id);

//         if (!$cliente) {
//             return response()->json(['message' => 'Cliente não encontrado'], 404);
//         }

//         $cliente->delete();
//         return response()->json(['message' => 'Cliente removido com sucesso']);
//     }
// }
