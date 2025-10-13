<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Arquivo",
 *     type="object",
 *     title="Arquivo",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="nome", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Arquivo extends Model
{
    protected $fillable = ['nome', 'link', 'id_pet'];

    public function pet() {
        return $this->belongsTo(Pet::class, 'id_pet');
    }
}
