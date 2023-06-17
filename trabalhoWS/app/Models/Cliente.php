<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    public $timestamps = false;
    protected $primaryKey = 'idCliente';


    protected $fillable = [
        'idCliente',
        'nomeCliente',
        'emailCliente',
        'telefoneCliente',
        'cpfCliente',
    ];
}