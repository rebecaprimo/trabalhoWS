<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Http\Resources\Api\HotelResource;
use App\Http\Requests\StoreHotelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     /**
* @OA\Get (
*   path="/api/hotel",
*   operationId="getHotelList",
*   tags={"Hotel"),
*   summary= "Retorna a lista de Hotéis",
*   description="Retorna o JSON da lista de Hotéis",
*   @OA\Response(
*       response=200,
*       description="Operação executada com sucesso"
*       )
*   )
*/
    public function index(Request $request)
    {
        $hotel = Hotel::query();
        $mensagem = __("hotel.listreturn");
        $codigoderetorno = 0;
    
        $filterParameter = $request->input("filtro");
        if ($filterParameter == null) {
            $mensagem = "Lista de hotéis retornada - completa";
            $codigoderetorno = 200;
        } else {
            [$filterCriteria, $filterValue] = explode(":", $filterParameter);
    
            if ($filterCriteria == "nomeHotel") {
                $hotel = $hotel->where("nomeHotel", "=", $filterValue);
                $mensagem = "Lista de hotéis retornada - Filtrada";
                $codigoderetorno = 200;
            } else {
                $hotel = [];
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
                        case "nomeHotel":
                            $hotel->orderBy('nomeHotel', $sortDirection);
                            break;
                        case "estrelasHotel":
                            $hotel->orderBy('estrelasHotel', $sortDirection);
                            break;
                        case "precoDiaria":
                            $hotel->orderBy('precoDiaria', $sortDirection);
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
            $hotel->offset(($page - 1) * $perPage)->limit($perPage);
    
            $recordsTotal = Hotel::count();
            $numberofPages = ceil($recordsTotal / $perPage);
            $mensagem = $mensagem . "+Paginada";
        }
    
        if ($codigoderetorno == 200) {
            $hotels = $hotel->get();
            $response = response()->json([
                'status' => 200,
                'mensagem' =>  __("hotel.listreturn"),
                'hoteis' => HotelResource::collection($hotels)
            ], 200);
        } else {
            $response = response()->json([
                'status' => 406,
                'mensagem' => $mensagem,
                'hoteis' => $hotel
            ], 406);
        }
    
        return $response;
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

    /**
    * @OA\Post (
    * path="/api/hotel",
    * operationId="storeHotel",
    * tags={"Hotel"},
    * summary="Cria uma novo Hotel",
    * description="Retorna o JSON com os dados da novo Hotel",
    * @OA\RequestBody(
    *       required=true,
    *       @OA\Jsoncontent(ref="#/components/schemas/StoreHotelRequest")
    *   ),
    *   @OA\Response(
    *       response=200, 
    *       description="Operação executada com sucesso",
    *       @OA\JsonContent(ref="#/components/schemas/Hotel")
    *       )
    *   )
    */
    public function store(Request $request)
    {
        $hotel = new Hotel;
        $hotel->nomeHotel = $request->nomeHotel;
        $hotel->enderecoHotel = $request->enderecoHotel;
        $hotel->telefoneHotel = $request->telefoneHotel;
        $hotel->estrelasHotel = $request->estrelasHotel;
        $hotel->precoDiaria = $request->precoDiaria;
        $hotel->save();

        return response () -> json([
            'status' => 200,
            'mensagem' => __("hotel.created"),
            'hotel' => new HotelResource($hotel)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */

    /**
    * @OA\Get(
    *   path="/api/hotel/{id}",
    *   operationId="getHotelById",
    *   tags={"Hotel"},
    *   summary="Retorna a informação de um hotel",
    *   description="Retorna o JSON da Categoria requisitada",
    *   @OA\Parameter(
    *       name="id",
    *       description="Id do Hotel",
    *       required=true,
    *       in="path",
    *       @OA\Schema(
    *           type="integer"
    *       )
    *    ),
    *    @OA\Response(
    *       response=200,
    *       description="Operação executada com sucesso"
    *       )
    *   )
    */
    public function show($idHotel)
    {
        try {
            $validator = Validator::make(['idHotel' => $idHotel], [
                'idHotel' => 'integer'
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['idHotel' => 'O campo Id deve ser numérico']);
            }

            $hotel = Hotel::findOrFail($idHotel);
            return response()->json([
                'status' => 200,
                'mensagem' => __("hotel.returned"),
                'hotel' => new HotelResource($hotel)
            ]);
        } catch(\Exception $ex) {
            $class = get_class($ex);
            switch($class) {
                case ModelNotFoundException::class:
                return response()-> json([
                    'status' => 404,
                    'mensagem' => 'Hotel não encontrado',
                    'hotel' => []
                ], 404);
                break;
                case \Illuminate\Validation\ValidationException::class:
                return response()-> json([
                    'status' => 406,
                    'mensagem' => $ex->getMessage(),
                    'hotel' => []
                ], 406);
                break;
                default:
                return response() -> json([
                    'status' => 500,
                    'mensagem' => 'Erro interno',
                    'hotel' => []
                    ], 500);
                break;
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function edit (Hotel $hotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */

    /**
    *   @OA\Patch(
    *   path="/api/hotel/{id}", 
    *   operationId="updateHotel", 
    *   tags={"Hotel"},
    *   summary="Atualiza um Hotel existente",
    *   description="Retorna o JSON do Hotel atualizado",
    *   @OA\Parameter(
    *       name="id",
    *       description="Id do Hotel", 
    *       required=true, 
    *       in="path",
    *       @OA\Schema(
    *           type="integer"
    *       )
    *   ),
    *   @OA\RequestBody(
    *       required=true, 
    *       @OA\JsonContent(ref="#/components/schemas/StoreHotelRequest")
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Operação executada com sucesso"
    *   )
    *)
    */
    public function update (StoreHotelRequest $request, Hotel $hotel)
    {
        $hotel = Hotel::find($hotel->idHotel);
        $hotel->nomeHotel = $request->nomeHotel;
        $hotel->enderecoHotel = $request->enderecoHotel;
        $hotel->telefoneHotel = $request->telefoneHotel;
        $hotel->estrelasHotel = $request->estrelasHotel;
        $hotel->precoDiaria = $request->precoDiaria;
        $hotel->update();

        return response () -> json([
            'status' => 200,
            'mensagem' => __("hotel.updated")
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */

    /**
    *   @OA\Delete(
    *   path="/api/hotel/{id}",
    *   operationId="deleteHotel",
    *   tags={"Hotel"},
    *   summary="Apaga um Hotel existente",
    *   description="Apaga um Hotel existente e não há retorno de dados",
    *   @OA\Parameter(
    *       name="id",
    *       description="Id do Hotel",
    *       required=true,
    *       in="path",
    *       @OA\Schema(
    *           type="integer"
    *       )
    *   ),
    *   @OA\Response(
    *       response=200, 
    *       description="Operação executada com sucesso",
    *       @OA\JsonContent()
    *   )
    * )
    */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return response() -> json([
            'status' => 200,
            'mensagem' => __("hotel.deleted")
        ], 200);
    }
}
