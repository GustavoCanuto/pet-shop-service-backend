<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArquivoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return Arquivo::paginate($perPage);
    }

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

    public function show(string $id)
    {
        return Arquivo::findOrFail($id);
    }

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
