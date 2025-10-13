<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return User::paginate($perPage);
    }

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

    public function show(string $id)
    {
        $user = User::with(['enderecos', 'pets'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

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
