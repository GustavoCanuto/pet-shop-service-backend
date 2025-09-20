<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('page', 10); // valor padrão = 10
        return Pet::paginate($perPage);
    }

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

    public function show(string $id)
    {
        return Pet::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $pet = Pet::findOrFail($id);
        $pet->update($request->all());
    }

    public function destroy(string $id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();
    }
}
