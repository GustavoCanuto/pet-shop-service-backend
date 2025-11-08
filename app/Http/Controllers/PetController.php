<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/pets",
     *     summary="Listar pets",
     *     @OA\Response(response=200, description="Lista de pets")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $query = Pet::query();

        // Filtrar por dono
        if ($request->has('id_dono')) {
            $idDono = $request->id_dono;

            $query->whereIn('id', function ($sub) use ($idDono) {
                $sub->select('id_pet')
                    ->from('user_pet') //
                    ->where('id_user', $idDono); //
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * @OA\Post(
     *     path="/api/pets",
     *     summary="Cadastrar pet",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Pet")
     *     ),
     *     @OA\Response(response=201, description="Pet criado")
     * )
     */
    public function store(Request $request)
    {
            DB::beginTransaction();

            try{
                $pet = Pet::create($request->json()->all());
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Pet cadastrado com sucesso!',
                    'data' => $pet
                ], 201);
            }
            catch (Exception $e){
               DB::rollBack();

               return response()->json(
                [
                'status' => false,
                'message' => 'Erro ao tentar cadastrar o pet'
                ], 400
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pets/{id}",
     *     summary="Mostrar pet",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pet encontrado")
     * )
     */
    public function show(string $id)
    {
        return Pet::findOrFail($id);
    }

    /**
    * @OA\Put(
        *     path="/api/pets/{id}",
        *     summary="Atualizar pet",
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(ref="#/components/schemas/Pet")
        *     ),
        *     @OA\Response(response=200, description="Pet atualizado")
        * )
        */
    public function update(Request $request, string $id)
    {
        $pet = Pet::findOrFail($id);
        $pet->update($request->all());
    }


    /**
     * @OA\Delete(
     *     path="/api/pets/{id}",
     *     summary="Deletar pet",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pet deletado")
     * )
     */
    public function destroy(string $id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();
    }
}
