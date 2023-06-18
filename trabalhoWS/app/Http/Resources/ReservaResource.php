<?php

namespace App\Http\Resources;

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
         // return parent::toArray($request);
         return [
            'id' => $this->idReserva,
            'idHotel' => $this->idHotel,
            'idCliente' => $this->idCliente,
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'numHospedes' => $this->numHospedes,
            'Cliente' => [
                'id' => $this->cliente->idCliente,
                'nome Cliente' => $this->cliente->nomeCliente,
            ],
        ];
    }
}
