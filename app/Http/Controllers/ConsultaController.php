<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
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
        $query = Consulta::query();

        // Filtrar por id do veterinário
        if ($request->has('id_veterinario')) {
            $query->where('id_veterinario', $request->id_veterinario);
        }

        // Filtrar por data (yyyy-mm-dd)
        if ($request->has('data')) {
            $query->whereDate('data', $request->data);
        }

        // Carregar dono e pet
        $query->with(['dono', 'pet', 'veterinario']);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $consultas = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $consultas
        ]);
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
        $validator = Validator::make($request->all(), [
            'data' => 'required|date_format:Y-m-d',
            'hora' => 'required|date_format:H:i',
            'id_veterinario' => 'required|integer',
            'id_dono' => 'required|integer',
            'id_pet' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }
        // Verifica se o veterinário existe e tem permissao 2
        $veterinario = \App\Models\User::where('id', $request->id_veterinario)
            ->where('permissao', 2)
            ->first();

        if (!$veterinario) {
            return response()->json([
                'status' => false,
                'message' => 'Veterinário não encontrado ou permissão inválida.'
            ], 400);
        }

        // Verifica se o dono existe e tem permissão 1
        $dono = \App\Models\User::where('id', $request->id_dono)
            ->where('permissao', 1)
            ->first();

        if (!$dono) {
            return response()->json([
                'status' => false,
                'message' => 'Dono não encontrado ou permissão inválida.'
            ], 400);
        }

        // Verifica se o pet existe e pertence ao dono
        $pet = \App\Models\Pet::find($request->id_pet);

        if (!$pet) {
            return response()->json([
                'status' => false,
                'message' => 'Pet não encontrado.'
            ], 400);
        }

        // verifica se relação existe na tabela pivô tbl_user_pet
        $petPertenceAoDono = DB::table('user_pet')
        ->where('id_user', $request->id_dono)
        ->where('id_pet', $request->id_pet)
        ->exists();

        if (!$petPertenceAoDono) {
            return response()->json([
                'status' => false,
                'message' => 'O pet informado não pertence ao dono.'
            ], 400);
        }

        // Verifica conflito de agenda (1 hora de intervalo)
        $dataConsulta = Carbon::parse($request->data . ' ' . $request->hora);

        $inicio = $dataConsulta->copy()->subHour();
        $fim = $dataConsulta->copy()->addHour();

        $existe = Consulta::where('id_veterinario', $request->id_veterinario)
            ->whereBetween(DB::raw("CONCAT(data, ' ', hora)"), [$inicio, $fim])
            ->exists();

        if ($existe) {
            return response()->json([
                'status' => false,
                'message' => 'Este veterinário já possui uma consulta marcada em um intervalo de 1 hora.'
            ], 409);
        }

        // Se passou em todas as verificações, cadastra
        DB::beginTransaction();
        try {
            $consulta = \App\Models\Consulta::create($request->all());
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
