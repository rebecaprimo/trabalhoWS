<?php

namespace App\Virtual\Models;

/**
* @OA\Schema(
*   title="Hotel",
*   description="Modelo de Hotel",
*   @OA\Xml(
*       name="Hotel"
*   )
* )
*/

class Hotel
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