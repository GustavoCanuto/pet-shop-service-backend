<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArquivoController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/arquivos",
     *     summary="Listar arquivos",
     *     @OA\Response(response=200, description="Lista de arquivos")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return Arquivo::paginate($perPage);
    }

    /**
     * @OA\Post(
     *     path="/api/arquivos",
     *     summary="Cadastrar arquivo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Arquivo")
     *     ),
     *     @OA\Response(response=201, description="Arquivo criado")
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $arquivo = Arquivo::create($request->json()->all());
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Arquivo cadastrado com sucesso!',
                'data' => $arquivo
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar arquivo'
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/arquivos/{id}",
     *     summary="Mostrar arquivo",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Arquivo encontrado")
     * )
     */
    public function show(string $id)
    {
        return Arquivo::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/arquivos/{id}",
     *     summary="Atualizar arquivo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Arquivo")
     *     ),
     *     @OA\Response(response=200, description="Arquivo atualizado")
     * )
     */
    public function update(Request $request, string $id)
    {
        $arquivo = Arquivo::findOrFail($id);
        $arquivo->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Arquivo atualizado com sucesso!',
            'data' => $arquivo
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/arquivos/{id}",
     *     summary="Deletar arquivo",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Arquivo deletado")
     * )
     */
    public function destroy(string $id)
    {
        $arquivo = Arquivo::findOrFail($id);
        $arquivo->delete();
        return response()->json([
            'status' => true,
            'message' => 'Arquivo deletado com sucesso!'
        ]);
    }
}
