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
    public function index(Request $request)
    {
        $hotel = Hotel::query();
        $mensagem = "Lista de hotéis retornada";
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
                'mensagem' => $mensagem,
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
            'mensagem' => 'Hotel criado',
            'hotel' => new HotelResource($hotel)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\Http\Response
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
                'mensagem' => 'Hotel retornado.',
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
            'mensagem' => 'Hotel atualizado'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return response() -> json([
            'status' => 200,
            'mensagem' => 'Hotel apagado'
        ], 200);
    }
}
