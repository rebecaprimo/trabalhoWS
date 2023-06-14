<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Http\Resources\HotelResource;
use App\Http\Requests\StoreHotelRequest;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        $hotel = Hotel:: all ();

        return response () -> json([
            'status' => 200,
            'mensagem' => 'Lista de hotéis retornada',
            'hotéis' => HotelResource::collection ($hotel)
        ], 200);
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
        $hotel = new Hotel();
        $hotel->nomeHotel = $request->nomeHotel;
        $hotel->enderecoHotel = $request->enderecoHotel;
        $hotel->telefoneHotel = $request->telefoneHotel;
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
    public function show(Hotel $hotel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function edit(Hotel $hotel)
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
        $hotel = Hotel::find($hotel->idCategoria);
        $hotel->nomeHotel = $request->nomeHotel;
        $hotel->enderecoHotel = $request->enderecoHotel;
        $hotel->telefoneHotel = $request->telefoneHotel;
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
