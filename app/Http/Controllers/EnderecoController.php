<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnderecoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10);
        return Endereco::paginate($perPage);
    }

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

    public function show(string $id)
    {
        return Endereco::findOrFail($id);
    }

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
