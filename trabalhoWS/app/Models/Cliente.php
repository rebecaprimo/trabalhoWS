<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $primaryKey = 'idCliente';
    protected $fillable = [
        'nomeCliente',
        'emailCliente',
        'telefoneCliente',
        'cpfCliente',
    ];
}
/*  */
