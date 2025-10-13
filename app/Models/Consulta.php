<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Consulta",
 *     type="object",
 *     title="Consulta",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="descricao", type="string"),
 *     @OA\Property(property="data", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
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
