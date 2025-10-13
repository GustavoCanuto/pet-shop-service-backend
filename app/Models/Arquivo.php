<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    protected $fillable = ['nome', 'link', 'id_pet'];

    public function pet() {
        return $this->belongsTo(Pet::class, 'id_pet');
    }
}
