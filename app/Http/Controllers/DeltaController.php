<?php

namespace App\Http\Controllers;

use App\Models\Delta;
use App\Models\Brand;
use App\Models\MainBrand;
use App\Models\ApiToken;
use App\Models\Platform;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Services\PostDecoder;

class DeltaController extends Controller
{
    // Método para criar um novo Delta
    public function create(Request $request)
    {
        // Valida os dados recebidos
        $validator = Validator::make($request->all(), [
            'week' => 'required|integer',
            'year' => 'required|integer',
            'brand_id' => 'required|exists:brand,id',
            'json' => 'json',
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
            'brand_id',
            'json',
        ]);

        $data['json'] = json_encode($data['json']);

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
            'json' => 'json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Atualiza os dados, mas não altera week e year
        $delta->update($request->except(['week', 'year']));

        if ($request->has('json')) {
            $delta->primary_posts = json_encode($request->input('json'));
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
            'brand_id' => 'required|exists:brand,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $deleted = Delta::where('week', $request->input('week'))
                        ->where('year', $request->input('year'))
                        ->where('brand_id', $request->input('brand_id'))
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
            'brand_id' => 'required|exists:brand,id',
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
        $delta = Delta::where('brand_id', $mainBrandId)
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
    
    /*
    // Explora o edge de posts, contabiliza mudanças nos posts do sistema e retorna os mais novos
    private function postExplorer($posts, $count, $type, $platform_id) {
        $newPosts = Array();
        for($i = 0; $i < $count; $i++) {
            if($type == 'instagram') {
                $post = $posts[$i]->node;
                $p = Post::where('internal_platform_id', $post->shortcode);

                if($p) {
                    $p->update([
                        'likes' => $post->edge_liked_by->count,
                        'num_comments' => $post->edge_media_to_comment->count
                    ]);
                    continue;
                }

                $tags = '';
                if (preg_match_all('/#\w+\s/', $input, $matches, PREG_PATTERN_ORDER)) {
                    foreach ($matches[1] as $word) {
                       $tags .= $word.', ';
                    }
                 }

                $mentions = '';
                if(count($post->edge_media_to_tagged_user->edges) > 0) {
                    foreach($post->edge_media_to_tagged_user->edges as $mention) {
                        $mentions .= $mention->node->user->username.', ';
                    }
                }

                $p = Post::create([
                    'platform_id' => $post->shortcode,
                    'url' => 'https://www.instagram.com/p/'.$post->shortcode,
                    'title' => '',
                    'description' => $post->edge_media_to_caption->edges[0]->node->text,
                    'tags' => $tags,
                    'likes' => $post->edge_liked_by->count,
                    'num_comments' => $post->edge_media_to_comment->count,
                    'is_video' => $post->is_video,
                    'is_image' => !$post->is_video,
                    'is_external' => false,
                    'item_url' => !$post->is_video ? $post->display_url : $post->video_url,
                    'platform_id' => $platform_id,
                    'mentions' => $mentions,
                    'internal_platform_id' => $platform_id
                ]);

                // Converte o timestamp para uma instância de Carbon
                $date = Carbon::createFromTimestamp($post->taken_at_timestamp);

                // Verifica se a data está na semana atual
                $isInCurrentWeek = $date->isSameWeek(Carbon::now());

                if ($isInCurrentWeek)
                    $newPosts[] = $p;
            }
        }

        return $newPosts;
    }
    */

    /**
     * Função deltaBuilder para construir o Delta com base nos dados fornecidos.
     */
    public function deltaBuilder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required|exists:brand,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Busca as informações de Brand
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

        $platforms = Platform::where('brand_id', $brand->id)->get();

        // Verifica se já existe um Delta da semana
         $delta = Delta::where('brand_id', $brand->id)
         ->where('week', date('W'))
         ->where('year', date('Y'))
         ->first();

         // Se não existir, cria
         if(!$delta)
            $delta = Delta::create([
                'week' => date('W'),
                'year' => date('Y'),
                'brand_id' => $brand->id
            ]);

        //Pega o delta da semana passada para comparar
        $lastDelta = Delta::where('brand_id', $brand->id)
        ->where('week', strval((intval(date('W')) - 1)))
        ->where('year', date('Y'))
        ->first();

        $decoder = new PostDecoder();

        $deltaWeek = new \stdClass();
        $deltaWeek->week = date('W');
        $deltaWeek->year = date('Y');
        $deltaWeek->brand_name = $brand->name;
        $deltaWeek->platforms = [];

        for($i = 0; $i < count($platforms); $i++) {
            $platform = $platforms[$i];

            // Lógica para capturar perfil e posts
            $sRequest = Request::create('/',
                'POST',
                ['id' => $platform->platform_id,
                'platform' => $platform->type,
                'type' => 'complete',
                'api_id' => $apiToken->id]);
            
            $response = $socialFetcher->fetchProfile($sRequest);  

            $json = (object) json_decode($response);
            $json = $json->data;

            $platform->description = $json->biography;
            $platform->num_followers = $json->edge_followed_by->count;
            $platform->avatar_url = $json->profile_pic_url;
            $platform->platform_id2 = $json->id;

            $platform->save();

            
            $deltaWeek->platforms[] = new \stdClass();
            $deltaWeek->platforms[$i]->id = $platform->id;
            $deltaWeek->platforms[$i]->followers = $json->edge_followed_by->count;
            $deltaWeek->platforms[$i]->total_platform_posts = $json->edge_owner_to_timeline_media->count;

            $postsDelta = 0;

            // Só para saber se a diferença de posts de uma semana a outra é maior que 9
            // Se for o caso, ele tem que chamar o fetchPosts
            if($lastDelta) {
                $lastJson =  (object) json_decode($lastDelta->json);
                if($lastJson && isset($lastJson->platforms)) {
                    $found = -1;
                    for($j = 0; $j < count($lastJson->platforms); $j++) {
                        if($lastJson->platforms[$j]->id == $platform->id) {
                            $found = $j;
                            break;
                        }
                    }

                    if($found != -1)
                        $postsDelta = $deltaWeek->platforms[$i]->total_platform_posts - $lastJson->platforms[$found]->total_platform_posts;
                }
            }

            if($postsDelta <= 9)
                $posts = $decoder->instagramDecoder($json->edge_owner_to_timeline_media, 'edges', 'month');
            else {
                $sRequest = Request::create('/',
                    'POST',
                    ['id' => $platform->platform_id2,
                    'platform' => $platform->type,
                    'date_range' => 'week',
                    'api_id' => $apiToken->id]);
                    
                $response = $socialFetcher->fetchPosts($sRequest);

                $json = (object) json_decode($response);
                $json = $json->data;
                $posts = $decoder->instagramDecoder($json->edge_owner_to_timeline_media, 'posts', 'none');
            }

            // Lógica para capturar comments
            if ($posts->count > 0) {
                foreach($posts->posts as $post) {
                    $sRequest = Request::create('/',
                        'POST',
                        ['id' => $post->shortcode,
                        'platform' => $platform->type,
                        'comments_limit' => 50,
                        'api_id' => $apiToken->id]);
                    
                    $response = $socialFetcher->fetchComments($sRequest);

                    $json = (object) $response;
                    $json = $json->data;
                    $post->comments = $decoder->instagramCommentDecoder($json->edge_media_to_comment);
                }
            }

            $deltaWeek->platforms[$i]->week_posts = $posts;
        }

        $delta->update([
            'json' => json_encode($deltaWeek)
        ]);

        $delta->save();

        return response()->json($deltaWeek, 200);
    }

    public function buildMonth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required|exists:brand,id',
            'month' => 'required|number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Busca as informações de Brand
        $brand = Brand::findOrFail($request->input('brand_id'));
        $month = $request->input('month');

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

        $platforms = Platform::where('brand_id', $brand->id)->get();

        $decoder = new PostDecoder();

        $deltaWeek = new \stdClass();
        $deltaWeek->month = date('m');
        $deltaWeek->year = date('Y');
        $deltaWeek->brand_name = $brand->name;
        $deltaWeek->platforms = [];

        for($i = 0; $i < count($platforms); $i++) {
            $platform = $platforms[$i];

            // Lógica para capturar perfil e posts
            $sRequest = Request::create('/',
                'POST',
                ['id' => $platform->platform_id,
                'platform' => $platform->type,
                'type' => 'complete',
                'api_id' => $apiToken->id]);
            
            $response = $socialFetcher->fetchProfile($sRequest);  

            $json = (object) json_decode($response);
            $json = $json->data;

            $platform->description = $json->biography;
            $platform->num_followers = $json->edge_followed_by->count;
            $platform->avatar_url = $json->profile_pic_url;
            $platform->platform_id2 = $json->id;

            $platform->save();
            
            $deltaWeek->platforms[] = new \stdClass();
            $deltaWeek->platforms[$i]->id = $platform->id;
            $deltaWeek->platforms[$i]->followers = $json->edge_followed_by->count;
            $deltaWeek->platforms[$i]->total_platform_posts = $json->edge_owner_to_timeline_media->count;

            $currentYear = Carbon::now()->year;

            // Criando a data com o primeiro dia do mês
            $startDate = Carbon::create($currentYear, $month, 1)->format('Y-m-d');
           
            $sRequest = Request::create('/',
                'POST',
                ['id' => $platform->platform_id2,
                'platform' => $platform->type,
                'start_date' => $startDate,
                'api_id' => $apiToken->id]);
                
            $response = $socialFetcher->fetchPosts($sRequest);

            $json = (object) json_decode($response);
            $json = $json->data;
            $posts = $decoder->instagramDecoder($json->edge_owner_to_timeline_media, 'posts', 'none');

            // Lógica para capturar comments
            if ($posts->count > 0) {
                foreach($posts->posts as $post) {
                    $sRequest = Request::create('/',
                        'POST',
                        ['id' => $post->shortcode,
                        'platform' => $platform->type,
                        'comments_limit' => 20,
                        'api_id' => $apiToken->id]);
                    
                    $response = $socialFetcher->fetchComments($sRequest);

                    $json = (object) $response;
                    $json = $json->data;
                    $post->comments = $decoder->instagramCommentDecoder($json->edge_media_to_comment);
                }
            }

            $deltaWeek->platforms[$i]->week_posts = $posts;
        }

        return response()->json($deltaWeek, 200);
    }
}
