<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $fillable = ['data', 'id_veterinario', 'id_dono', 'id_pet'];

    public function veterinario() {
        return $this->belongsTo(User::class, 'id_veterinario');
    }

    public function dono() {
        return $this->belongsTo(User::class, 'id_dono');
    }

    public function pet() {
        return $this->belongsTo(Pet::class, 'id_pet');
    }
}
