<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reservas;

class Hoteis extends Model
{
    use HasFactory;

    protected $primaryKey = 'idHotel';
    public $timestamps = false;

    protected $fillable = [
        'nomeHotel',
        'enderecoHotel',
        'telefoneHotel',
        'estrelaHotel',
        'precoDiaria',
    ];

    protected $casts = [
        'precoDiaria' => 'decimal:10,2',
    ];
}
