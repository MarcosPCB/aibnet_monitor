<?php

namespace App\Http\Controllers;

use App\Models\MainBrand;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Services\LLMComm;

class MainBrandController extends Controller
{
    // Cria uma MainBrand com uma Brand principal e possíveis oponentes
    public function create(Request $request, $account_id)
    {
        // Validação básica (pode ser aprimorada)
        $request->validate([
            'name' => 'required|string|max:255',
            'main_brand_id' => 'required|exists:brand,id',
            'opponents' => 'array',
            'opponents.*' => 'exists:brand,id',
            'chat_model' => 'string'
        ]);

        // Criação da MainBrand
        $mainBrand = MainBrand::create([
            'name' => $request->name,
            'account_id' => $account_id, // Assumindo que account_id é passado
            'follow_tags' => $request->follow_tags,
            'mentions' => $request->mentions,
            'past_stamp' => $request->past_stamp,
            'chat_model' => $request->chat_model,
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

    public function buildDelta($id) {
        $mainBrand = MainBrand::findOrFail($id);

        $primary = $mainBrand->primaryBrand()->first();
        $opponents = $mainBrand->opponents()->get();

        $delta = new DeltaController();

        $sRequest = Request::create('/', 'POST', [
            'brand_id' => $primary->id,
        ]);

        $primaryJson = $delta->deltaBuilder($sRequest)->getData();

        $opponentsJson = [];

        if($opponents) {
            foreach($opponents as $brand) {
                $sRequest = Request::create('/', 'POST', [
                    'brand_id' => $brand->id,
                ]);
        
                $opponentsJson[] = $delta->deltaBuilder($sRequest)->getData();
            }
        }

        $completeDelta = new \stdClass();
        $completeDelta->primary_brand = $primaryJson;
        $completeDelta->opponents = $opponentsJson;

        $llm = new LLMComm($id);

        $report = $llm->generateReport($completeDelta->primary_brand);

        if(!$report)
            return response()->json([
                'error' => 'report-generation'
                ], 500);

        $result = $llm->storeReport($completeDelta->primary_brand->brand_name, $report);

        if(!$result)
            return response()->json([
                'error' => 'report-store'
                ], 500);

        return response()->json('Success', 200);
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
        $opponents = $mainBrand->opponents()->get();
        $primary = $mainBrand->primaryBrand()->first();
        return response()->json([
            'mainBrand' => $mainBrand,
            'opponents' => $opponents,
            'primary'=> $primary
        ], 200);
    }

    public function getPlatforms($id) {
        $mainBrand = MainBrand::find($id);
        $primaryBrand = $mainBrand->primaryBrand()->first(); // Pegando a primeira primaryBrand

        $platforms = $primaryBrand ? $primaryBrand->platforms : collect(); // Verifica se há uma primaryBrand antes de acessar platforms

        return response()->json($platforms, 200);
    }

    public function attachBrand($id, Request $request) {
        $mainBrand = MainBrand::findOrFail($id);

        $request->validate([
            'brand_id' => 'required|exists:brand,id',
            'is_opponent' => 'required|boolean',
        ]);

        $brand = Brand::findOrFail($request->only('brand_id'));

        $is_opponent = $request->only('is_opponent');

        if(!$is_opponent) {
            $primary = $mainBrand->primaryBrand()->first();
            $mainBrand->brands()->detach($primary->id);
            $mainBrand->attach($brand);

            return response()->json('Success', 200);
        }

        $mainBrand->brands()->attach($brand[0]->id, ['is_opponent'=> true]);
        return response()->json('Success', 200);
    }

    public function detachBrand($id, Request $request) {
        $mainBrand = MainBrand::findOrFail($id);

        $request->validate([
            'brand_id' => 'required|exists:brand,id',
        ]);

        $brand = Brand::findOrFail($request->only('brand_id'));

        $mainBrand->brands()->detach($brand[0]->id);

        return response()->json('Success', 200);
    }


}

