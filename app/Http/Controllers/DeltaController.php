<?php

namespace App\Http\Controllers;

use App\Models\Delta;
use App\Models\Brand;
use App\Models\MainBrand;
use App\Models\ApiToken;
use App\Models\Platform;
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

    // Função para encontrar um Delta existente com base em main_brand_id, week, year ou data específica
    public function findDelta(Request $request)
    {
        // Validação dos dados fornecidos
        $request->validate([
            'main_brand_id' => 'required|exists:main_brand,id',
            'use_current_week' => 'sometimes|boolean',
            'week' => 'required_without_all:use_current_week,date|integer',
            'year' => 'required_without_all:use_current_week,date|integer',
            'date' => 'required_without_all:use_current_week,week,year|date', // Novo campo opcional para data específica
        ]);

        $mainBrandId = $request->input('main_brand_id');

        // Verifica se a pessoa quer usar a semana atual
        if ($request->input('use_current_week')) {
            $week = date('W'); // Semana atual
            $year = date('Y'); // Ano atual
        } elseif ($request->has('date')) {
            // Se a pessoa forneceu uma data específica, converte para semana e ano
            $date = strtotime($request->input('date'));
            $week = date('W', $date);
            $year = date('Y', $date);
        } else {
            // Usa semana e ano fornecidos
            $week = $request->input('week');
            $year = $request->input('year');
        }

        // Busca por um Delta com os parâmetros fornecidos
        $delta = Delta::where('main_brand_id', $mainBrandId)
                      ->where('week', $week)
                      ->where('year', $year)
                      ->first();

        if ($delta) {
            return response()->json([
                'message' => 'Delta found',
                'delta' => $delta,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Delta not found',
            ], 404);
        }
    }

    /**
     * Função deltaBuilder para construir o Delta com base nos dados fornecidos.
     */
    public function deltaBuilder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'main_brand_id' => 'required|exists:main_brand,id',
            'brand_id' => 'required|exists:brand,id',
            'is_opponent' => 'required|boolean',
            'capture' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $capture = $request->input('capture');

        // Busca as informações da MainBrand e Brand
        $mainBrand = MainBrand::findOrFail($request->input('main_brand_id'));
        $brand = Brand::findOrFail($request->input('brand_id'));

        // Avalia qual ApiToken pode ser usado (a lógica exata de escolha pode variar)
        $apiToken = ApiToken::where(function ($query) {
            $query->whereColumn('limit_used', '<', 'limit')
                  ->orWhere(function ($query) {
                      $query->whereRaw('CASE
                          WHEN limit_type = "daily" AND DATE(last_used) < CURDATE() THEN TRUE
                          WHEN limit_type = "weekly" AND YEARWEEK(last_used, 1) < YEARWEEK(CURDATE(), 1) THEN TRUE
                          WHEN limit_type = "monthly" AND DATE_FORMAT(last_used, "%Y-%m") < DATE_FORMAT(CURDATE(), "%Y-%m") THEN TRUE
                          WHEN limit_type = "yearly" AND YEAR(last_used) < YEAR(CURDATE()) THEN TRUE
                          ELSE FALSE
                      END');
                  });
        })
        ->where('status', true)
        ->where('expires', '>', now())  // Verifica se o token ainda não expirou
        ->firstOrFail();        

        // Instancia o controlador SocialFetcher
        $socialFetcher = new SocialFetcherController();

        // Lógica de captura com base no bitwise de 'capture'
        $platforms = Platform::where('brand_id', $brand->id)->get();

        foreach ($platforms as $platform) {
            // Lógica para capturar perfil
            if ($capture == 0) {
                $sRequest = Request::create('/',
                    'POST',
                    ['id' => $platform->platform_id,
                    'platform' => $platform->type,
                    'api_id' => $apiToken->id]);
                
                $response = $socialFetcher->fetchProfile($sRequest);
                return $response;
            }

            // Lógica para capturar posts
            if ($capture & 1) {
                app(SocialFetcherController::class)->fetchPosts($apiToken, $platform->url, $request->input('start_date'), $request->input('end_date'));
            }

            // Lógica para capturar comments
            if ($capture & 2) {
                app(SocialFetcherController::class)->fetchComments($apiToken, $platform->url, $request->input('post_id'));
            }
        }

        // Aqui você pode adicionar a lógica de processamento do Delta após as capturas
        // e salvar o Delta no banco de dados, se necessário.

        return response()->json(['message' => 'Delta construído com sucesso!'], 200);
    }
}
