<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return Consulta::paginate($perPage);
    }

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

    public function show(string $id)
    {
        return Consulta::findOrFail($id);
    }

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
