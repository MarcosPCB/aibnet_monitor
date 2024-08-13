<?php

namespace App\Http\Controllers;

use App\Models\MainBrand;
use App\Models\Brand;
use Illuminate\Http\Request;

class MainBrandController extends Controller
{
    // Cria uma MainBrand com uma Brand principal e possíveis oponentes
    public function create(Request $request)
    {
        // Validação básica (pode ser aprimorada)
        $request->validate([
            'name' => 'required|string|max:255',
            'main_brand_id' => 'required|exists:brand,id',
            'opponents' => 'array',
            'opponents.*' => 'exists:brand,id'
        ]);

        // Criação da MainBrand
        $mainBrand = MainBrand::create([
            'name' => $request->name,
            'account_id' => $request->account_id, // Assumindo que account_id é passado
            'follow_tags' => $request->follow_tags,
            'mentions' => $request->mentions,
            'past_stamp' => $request->past_stamp,
        ]);

        // Associar a Brand principal
        $mainBrand->brands()->attach($request->main_brand_id, ['is_opponent' => false]);

        // Associar os opponents se existirem
        if ($request->has('opponents')) {
            foreach ($request->opponents as $opponentId) {
                $mainBrand->brands()->attach($opponentId, ['is_opponent' => true]);
            }
        }

        return response()->json($mainBrand, 201);
    }

    // Atualiza uma MainBrand existente
    public function update(Request $request, $id)
    {
        $mainBrand = MainBrand::findOrFail($id);

        // Atualiza os campos da MainBrand
        $mainBrand->update($request->all());

        return response()->json($mainBrand, 200);
    }

    // Deleta uma MainBrand e suas associações
    public function delete($id)
    {
        $mainBrand = MainBrand::findOrFail($id);

        // Remove as associações com as brands
        $mainBrand->brands()->detach();

        // Deleta a MainBrand
        $mainBrand->delete();

        return response()->json(['message' => 'MainBrand deleted successfully'], 200);
    }

    public function get($id)
    {
        $mainBrand = MainBrand::findOrFail($id);
        return response()->json($mainBrand, 200);
    }
}

