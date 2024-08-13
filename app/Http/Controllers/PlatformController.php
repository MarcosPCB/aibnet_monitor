<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    /**
     * Cria uma nova platform.
     */
    public function create(Request $request)
    {
        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'platform_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'avatar_url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'num_followers' => 'nullable|integer|min:0',
            'num_likes' => 'nullable|integer|min:0',
            'capture_comments' => 'boolean',
            'capture_users_from_comments' => 'boolean',
            'active' => 'boolean',
            'brand_id' => 'required|exists:brand,id',
        ]);

        // Verificação se a plataforma já existe
        $exists = Platform::where('platform_id', $validatedData['platform_id'])
                          ->where('url', $validatedData['url'])
                          ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A Platforma já existe no banco de dados.',
            ], 409); // HTTP 409 Conflict
        }

        // Criação da platform
        try {
            $platform = Platform::create($validatedData);

            return response()->json([
                'message' => 'Platforma criada com sucesso!',
                'platform' => $platform
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar Platforma.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza uma platform existente.
     */
    public function update(Request $request, $id)
    {
        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'type' => 'sometimes|string|max:255',
            'url' => 'sometimes|string|max:255',
            'platform_id' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'avatar_url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'num_followers' => 'sometimes|integer|min:0',
            'num_likes' => 'sometimes|integer|min:0',
            'capture_comments' => 'sometimes|boolean',
            'capture_users_from_comments' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
            'brand_id' => 'sometimes|exists:brand,id',
        ]);

        // Atualização da platform
        $platform = Platform::findOrFail($id);

        try {
            $platform->update($validatedData);

            return response()->json([
                'message' => 'Platforma atualizada com sucesso!',
                'platform' => $platform
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar a Platforma.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica se uma plataforma já existe no banco de dados com base no platform_id e url.
     */
    public function check(Request $request)
    {
        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'platform_id' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        // Verificação da existência da plataforma
        $exists = Platform::where('platform_id', $validatedData['platform_id'])
                          ->where('url', $validatedData['url'])
                          ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A Platforma já existe no banco de dados.',
            ], 200);
        } else {
            return response()->json([
                'message' => 'A Platforma não foi encontrada no banco de dados.',
            ], 404);
        }
    }

    // Método para desativar uma plataforma (soft delete)
    public function deactivate($id)
    {
        $platform = Platform::findOrFail($id);
        $platform->active = false;
        $platform->save();

        return response()->json(['message' => 'Platform deactivated'], 200);
    }

    // Método para deletar uma plataforma (hard delete)
    public function destroy($id)
    {
        $platform = Platform::findOrFail($id);
        $platform->delete();

        return response()->json(['message' => 'Platform deleted'], 200);
    }

    public function get($id)
    {
        $platform = Platform::findOrFail($id);
        return response()->json($platform, 200);
    }
}
