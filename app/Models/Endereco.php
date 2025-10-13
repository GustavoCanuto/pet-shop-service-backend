<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $fillable = ['logradouro', 'complemento', 'cidade', 'uf', 'id_usuario'];

    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
