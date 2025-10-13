<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/consultas",
     *     summary="Listar consultas",
     *     @OA\Response(response=200, description="Lista de consultas")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return Consulta::paginate($perPage);
    }

    /**
     * @OA\Post(
     *     path="/api/consultas",
     *     summary="Cadastrar consulta",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     *     @OA\Response(response=201, description="Consulta criada")
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $consulta = Consulta::create($request->json()->all());
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Consulta cadastrada com sucesso!',
                'data' => $consulta
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar consulta'
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultas/{id}",
     *     summary="Mostrar consulta",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Consulta encontrada")
     * )
     */
    public function show(string $id)
    {
        return Consulta::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/consultas/{id}",
     *     summary="Atualizar consulta",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     *     @OA\Response(response=200, description="Consulta atualizada")
     * )
     */
    public function update(Request $request, string $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Consulta atualizada com sucesso!',
            'data' => $consulta
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/consultas/{id}",
     *     summary="Deletar consulta",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Consulta deletada")
     * )
     */
    public function destroy(string $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();
        return response()->json([
            'status' => true,
            'message' => 'Consulta deletada com sucesso!'
        ]);
    }
}
