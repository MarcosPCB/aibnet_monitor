<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    // Método para criar uma nova Brand e registrar as Platforms
    public function create(Request $request)
    {
        // Validação dos dados da Brand
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platforms' => 'required|array',
            'platforms.*.type' => 'required|string',
            'platforms.*.url' => 'required|url',
            'platforms.*.platform_id' => 'required|string',
            'platforms.*.name' => 'required|string',
            'platforms.*.avatar_url' => 'nullable|url',
            'platforms.*.description' => 'nullable|string',
            'platforms.*.tags' => 'nullable|string',
            'platforms.*.num_followers' => 'nullable|integer',
            'platforms.*.num_likes' => 'nullable|integer',
            'platforms.*.capture_comments' => 'nullable|boolean',
            'platforms.*.capture_users_from_comments' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $platforms = $request->input('platforms'); // Assumindo que as platforms são enviadas em um array
        $platformExists = false;

        foreach ($platforms as $platformData) {
            $response = Http::post(route('platform.check'), $platformData);

            if ($response->status() == 200 && $response->json() === true) {
                $platformExists = true;
                break;
            }
        }

        if ($platformExists) {
            return response()->json(['message' => 'One or more platforms already exist'], 409);
        }

        // Criação da Brand, caso nenhuma platform já exista
        $brand = Brand::create($request->except('platforms'));

        // Registro das platforms associadas
        foreach ($platforms as $platformData) {
            $platformData['brand_id'] = $brand->id; // Associa a platform à brand recém-criada
            Platform::create($platformData);
        }

        return response()->json($brand, 201);
    }

    // Método para atualizar uma Brand
    public function update(Request $request, $id)
    {
        // Validação dos dados da Brand
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            // Outras validações conforme necessário
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $brand = Brand::findOrFail($id);
        $brand->update($request->all());

        return response()->json($brand, 200);
    }

    public function get($id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json($brand, 200);
    }
}
