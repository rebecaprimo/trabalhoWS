<?php

namespace App\Http\Controllers\api;

use App\Models\Cliente;
use App\Models\Reservas;
use App\Models\Hoteis;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\ReservaResource;



class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

          $query = Reservas::with('cliente', 'hoteis');
          $mensagem = "Lista de reservas retornada";
          $codigoretorno = 0;

          //OBTEM PARAMETRO DO FILTRO
          $filterParameter = $request ->input("filtro");

          //$f nao ha um parametro;
          if($filterParameter == null) {
               //Retorna todos as reservas & Default
               $mensagem = "Lista de reservas retornada - Completa";
               $codigoretorno = 200;
          } else {
               //Obtem o nome do filtro e o criteiro
               [$filterCriteria, $filterValue] = explode(":", $filterParameter);

               //Se o filtro está adequado
               if($filterCriteria == "nome_cliente") {
                   //Faz inner join para obter o Cliente
                   $reservas = $query->join("clientes", "idCliente", "=", "idCliente")
                                                 ->where("nomeCliente", "=", $filterValue);
                   $mensagem = "Lista de reservas retornada - Filtrada";
                   $codigoderetorno = 200;
               } else {
                   //Usuario chamou um filtro que não existe, então nao ha nada a retornadar (Error 406 - No Accepted)
                   $reservas = [ ];
                   $mensagem = "Filtro nao aceito";
                   $codigoretorno = 406;
               }
          }

          if($codigoretorno == 200) {
               //Retorno o processamento da ordenacao

               //se há input para ornedacao

               if($request->input('ordenacao' , ' ')) {
                   $sorts = explode (' , ' , $request->input('ordenacao', ' '));
                   foreach($sorts as $sortColumn) {
                       $sortDirection = Str::startsWith($sortColumn, ' - ')? 'desc' : 'asc';
                       $sortColumn = ltrim($sortColumn, ' - ');


                       //Transforma os nomes dos parametros em nomes dos campos de Modelo
                       switch($sortColumn) {
                            case("dataInicio");
                               $query->orderBy('dataInicio', $sortDirection);
                               break;
                           case("dataFim");
                               $query->orderBy('dataFim', $sortDirection);
                               break;
                           case("numHospedes");
                               $query->orderBy('numHospedes', $sortDirection);
                               break;

                           }
                       }
                       $mensagem = $mensagem . "+Ordenada";
                   }
                   $input = $request->input('pagina');
                   if($input) {
                        $page = $input;
                        $perPage = 3; //Registros por pagina
                        $query->offset(($page-1)  * $perPage)->limit($perPage);
                        $reservas = $query->get( );

                        $recordsTotal = Reservas::count( );
                        $numberOfPages = ceil($recordsTotal / $perPage);

                        $mensagem = $mensagem . "+Paginada";
                   }

                   //Processamento foi ok, reotrna com base no criterio
                   if($codigoretorno == 200) {
                        $reservas = $query->get( );
                        $response = response( ) -> json([
                            'status' => 200,
                            'mensagem' => $mensagem,
                            'reservas' => ReservaResource::collection($reservas)
                        ], 200);


                   } else {
                        //Retorna o erro que ocorreu
                        $response = response()->json([
                           'status' => 406,
                           'mensagem' => $mensagem,
                           'reservas' => $reservas
                        ], 406);
                   }

                   return $response;

            }
          }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
