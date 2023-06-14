<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $table = 'HOTEL';
    protected $primaryKey = 'idHotel';
    protected $fillable = ['nomeHotel', 'enderecoHotel', 'telefoneHotel', 'estrelasHotel', 'precoDiaria'];
    public $incrementing = true;
    public $timestamps = false;
}
