<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;

class Cliente extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $primaryKey = 'idCliente';
    protected $fillable = [
        'nomeCliente',
        'emailCliente',
        'telefoneCliente',
        'cpfCliente',
        'senha',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    public $timestamps = false;

    public function getAuthPassword() {
        return $this->senha; //coluna real que 'substitui' a coluna 'password'
    }
}
