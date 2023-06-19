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
        $cliente->senha = bcrypt($request->senha);

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
        $cliente->senha = bcrypt($request->senha);



        $cliente->update();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        return response() -> json([
            'status' => 200,
            'mensagem' => __("cliente.updated"),
        ], 200);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

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
