<?php

namespace App\Http\Controllers;

use App\Models\Delta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeltaController extends Controller
{
    // Método para criar um novo Delta
    public function create(Request $request)
    {
        // Valida os dados recebidos
        $validator = Validator::make($request->all(), [
            'week' => 'required|integer',
            'year' => 'required|integer',
            'main_brand_id' => 'required|exists:main_brand,id',
            'primary_posts' => 'array',
            'primary_posts.*' => 'integer|exists:post,id',
            'opponents_posts' => 'array',
            'opponents_posts.*' => 'integer|exists:post,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Converte os arrays para JSON
        $data = $request->only([
            'week',
            'year',
            'main_brand_id',
            'primary_posts',
            'opponents_posts'
        ]);

        $data['primary_posts'] = json_encode($data['primary_posts']);
        $data['opponents_posts'] = json_encode($data['opponents_posts']);

        // Cria o Delta
        $delta = Delta::create($data);

        return response()->json($delta, 201);
    }

    // Método para atualizar um Delta existente
    public function update(Request $request, $id)
    {
        $delta = Delta::findOrFail($id);

        // Valida os dados recebidos
        $validator = Validator::make($request->all(), [
            'primary_posts' => 'array',
            'primary_posts.*' => 'integer|exists:post,id',
            'opponents_posts' => 'array',
            'opponents_posts.*' => 'integer|exists:post,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Atualiza os dados, mas não altera week e year
        $delta->update($request->except(['week', 'year']));

        if ($request->has('primary_posts')) {
            $delta->primary_posts = json_encode($request->input('primary_posts'));
        }

        if ($request->has('opponents_posts')) {
            $delta->opponents_posts = json_encode($request->input('opponents_posts'));
        }

        $delta->save();

        return response()->json($delta, 200);
    }

    // Método para buscar Delta por data
    public function findByDate(Request $request)
    {
        $date = $request->input('date'); // Formato esperado: 'Y-m-d'

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $week = date('W', strtotime($date));
        $year = date('Y', strtotime($date));

        $delta = Delta::where('week', $week)
                      ->where('year', $year)
                      ->first();

        if (!$delta) {
            return response()->json(['message' => 'Delta not found'], 402);
        }

        return response()->json($delta, 200);
    }

    // Método para deletar um Delta
    public function delete(Request $request, $id = null)
    {
        if ($id) {
            $delta = Delta::findOrFail($id);
            $delta->delete();
            return response()->json(['message' => 'Delta deleted successfully'], 200);
        }

        // Deletar usando week, year e main_brand_id
        $validator = Validator::make($request->all(), [
            'week' => 'required|integer',
            'year' => 'required|integer',
            'main_brand_id' => 'required|exists:main_brand,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $deleted = Delta::where('week', $request->input('week'))
                        ->where('year', $request->input('year'))
                        ->where('main_brand_id', $request->input('main_brand_id'))
                        ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Delta deleted successfully'], 200);
        }

        return response()->json(['message' => 'Delta not found'], 402);
    }
}
