<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel;
use App\Models\Cliente;

class Reserva extends Model
{
    
    protected $table = 'reservas';

    protected $fillable = [
        'idHotel',
        'idCliente',
        'dataInicio',
        'dataFim',
        'numHospedes',
    ];
    
    public $timestamps = false;
    protected $primaryKey = 'idReserva';

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente');
    }
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'idHotel');
    }


}
