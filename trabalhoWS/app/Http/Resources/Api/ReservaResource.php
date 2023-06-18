<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'idReserva' => $this->idReserva,
            'idHotel' => $this->idHotel,
            'idCliente' => $this->idCliente,
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'numHospedes' => $this->numHospedes,
            
        ];
    }
}
