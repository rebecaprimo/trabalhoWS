<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
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
            'id' => $this->idCliente,
            'nome_cliente' => $this->nomeCliente,
            'email_cliente' => $this->emailCliente,
            'telefone_cliente' => $this->telefoneCliente,
            'cpf_cliente' => $this->cpfCliente,
        ];
    }
}
