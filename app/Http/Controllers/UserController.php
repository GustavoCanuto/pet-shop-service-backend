<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Listar usuários",
     *     @OA\Response(response=200, description="Lista de usuários")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return User::paginate($perPage);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Cadastrar usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=201, description="Usuário criado")
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->json()->all());
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Usuário cadastrado com sucesso!',
                'data' => $user
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar usuário'
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Mostrar usuário",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Usuário encontrado")
     * )
     */
    public function show(string $id)
    {
        $user = User::with(['enderecos', 'pets'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Atualizar usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=200, description="Usuário atualizado")
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $user
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Deletar usuário",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Usuário deletado")
     * )
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => 'Usuário deletado com sucesso!'
        ]);
    }

     // Relacionamento N:N - adicionar pet
    /**
     * @OA\Post(
     *     path="/api/users/{userId}/pets/{petId}",
     *     summary="Adicionar pet ao usuário",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="petId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pet vinculado ao usuário")
     * )
     */
    public function addPet($userId, $petId)
    {
        $user = User::findOrFail($userId);
        $user->pets()->attach($petId);

        return response()->json([
            'status' => true,
            'message' => 'Pet vinculado ao usuário com sucesso!'
        ]);
    }

    // Relacionamento N:N - remover pet
    /**
     * @OA\Delete(
     *     path="/api/users/{userId}/pets/{petId}",
     *     summary="Remover pet do usuário",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="petId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pet desvinculado do usuário")
     * )
     */
    public function removePet($userId, $petId)
    {
        $user = User::findOrFail($userId);
        $user->pets()->detach($petId);

        return response()->json([
            'status' => true,
            'message' => 'Pet desvinculado do usuário com sucesso!'
        ]);
    }
}
