<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hoteis;
use App\Models\Cliente;

class Reservas extends Model
{
    use HasFactory;

    protected $table = 'reservas';
    protected $primaryKey = 'idReserva';
    public $timestamps = false;

    protected $fillable = [
        // 'idHotel',
        // 'idCliente',
        'dataInicio',
        'dataFim',
        'numHospedes',
    ];

    public function hoteis()
    {
        return $this->belongsTo(Hoteis::class, 'idHotel', 'idHotel');
    }


    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }
}
