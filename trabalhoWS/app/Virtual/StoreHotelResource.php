<?php

namespace App\Virtual;

/**
* @OA\Schema(
*   title="Requisição para novo Hotel",
*   description="Requisição enviada via Body para novo Hotel",
*   type="object",
*   required={"nomeHotel}
* )
*/

class StoreHotelRequest
{
    /**
     * @OA\Property(
     *  title="ID",
     *  description="ID",
     *  format="int64",
     *  example=1
     * )
     * @var integer
     */

    private $idHotel;
     
    /**
    * @OA\Property(
    * title="nome do Hotel", 
    * description="nome do Hotel"
    * example="Hotel Copacabana"
    * )
    * @var string
    */

    public $nomeHotel;
}