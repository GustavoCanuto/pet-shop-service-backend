<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Endereco",
 *     type="object",
 *     title="Endereco",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="logradouro", type="string"),
 *     @OA\Property(property="numero", type="string"),
 *     @OA\Property(property="cidade", type="string"),
 *     @OA\Property(property="estado", type="string"),
 *     @OA\Property(property="cep", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Endereco extends Model
{
    protected $fillable = ['logradouro', 'complemento', 'cidade', 'uf', 'id_usuario'];

    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
