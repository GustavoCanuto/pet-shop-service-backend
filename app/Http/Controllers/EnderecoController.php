<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnderecoController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/enderecos",
     *     summary="Listar endereços",
     *     @OA\Response(response=200, description="Lista de endereços")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return Endereco::paginate($perPage);
    }

    /**
     * @OA\Post(
     *     path="/api/enderecos",
     *     summary="Cadastrar endereço",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Endereco")
     *     ),
     *     @OA\Response(response=201, description="Endereço criado")
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $endereco = Endereco::create($request->json()->all());
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Endereço cadastrado com sucesso!',
                'data' => $endereco
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar endereço'
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/enderecos/{id}",
     *     summary="Mostrar endereço",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Endereço encontrado")
     * )
     */
    public function show(string $id)
    {
        return Endereco::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/enderecos/{id}",
     *     summary="Atualizar endereço",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Endereco")
     *     ),
     *     @OA\Response(response=200, description="Endereço atualizado")
     * )
     */
    public function update(Request $request, string $id)
    {
        $endereco = Endereco::findOrFail($id);
        $endereco->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Endereço atualizado com sucesso!',
            'data' => $endereco
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/enderecos/{id}",
     *     summary="Deletar endereço",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Endereço deletado")
     * )
     */
    public function destroy(string $id)
    {
        $endereco = Endereco::findOrFail($id);
        $endereco->delete();
        return response()->json([
            'status' => true,
            'message' => 'Endereço deletado com sucesso!'
        ]);
    }
}
