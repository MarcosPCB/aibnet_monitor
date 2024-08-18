<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class SocialFetcherController extends Controller
{
    private function updateApiTokenUsage($id, $usedAmount)
    {
        $api = new ApiTokenController();
        //$api->restartLimit(Request::create('/', 'POST', ['id' => $id]));

        $apiToken = ApiToken::where('id', $id)->first();
        if ($apiToken) {
            $apiToken->limit_used += $usedAmount;
            $apiToken->last_used = date_format(now(), 'Y-m-d H:i:s');
            $apiToken->save();
        }
    }

    public function fetchProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'platform' => 'required|string',
            'type' => 'required|in:complete,likes,basic',
            'api_id' => 'required|exists:api_tokens,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $id = $request->input('id');
        $platform = $request->input('platform');
        $type = $request->input('type');
        $apiToken = $request->input('api_id');
        
        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        $response = null;

        switch($type) {
            case 'complete':
                //$response = Http::withoutVerifying()->get($apiTokenData->url.$platform.'/user/detailed-info?username='.$id.'&token='.$apiTokenData->token);
                $filePath = base_path('tests/Mocks/complete-profile.json');

                // Verifica se o arquivo existe
                if (File::exists($filePath)) {
                    // Lê o conteúdo do arquivo
                    $jsonContent = File::get($filePath);

                    // Retorna o conteúdo como JSON
                    //return response()->json(json_decode($jsonContent), 200);
                    $response = json_decode($jsonContent);
                }
                break;
            
            case 'likes':
                $response = Http::withoutVerifying()->get($apiTokenData->url.$platform.'/user/followers?user_id='.$id.'&token='.$apiTokenData->token);
                break;

            case 'basic':
                $response = Http::withoutVerifying()->get($apiTokenData->url.$platform.'/user/info?username='.$id.'&token='.$apiTokenData->token);
                break;
        }

        $cost = 1;
        if($platform == 'instagram') {

            switch($type) {
                case 'complete':
                    $cost = 10;
                    break;

                case 'likes':
                    $cost = 2;
                    break;

                case 'basic':
                    $cost = 3;
                    break;
            }
        }

        //$this->updateApiTokenUsage($apiToken, $cost);

        return $response;//->json();
    }

    public function fetchPosts(Request $request)
    {
        $id = $request->input('id');
        $platform = $request->input('platform');
        $apiToken = $request->input('api_id');
        $dateRange = $request->input('date_range'); // Pode ser 'today', 'week', 'month', 'year'
        $startDate = $request->input('start_date'); // Data de início para intervalos específicos (formato: 'YYYY-MM-DD')

        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        $timestamp = 0;
        $depth = 1;
        $chunk = 10;

        if ($dateRange) {
            switch($dateRange) {
                case 'today':
                    $timestamp = Carbon::today()->timestamp;
                    break;

                case 'week':
                    $timestamp = Carbon::now()->startOfWeek(Carbon::SUNDAY)->timestamp;
                    $depth = 3;
                    break;

                case 'month':
                    $timestamp = Carbon::now()->startOfMonth()->timestamp;
                    $depth = 10;
                    break;

                case 'year':
                    $timestamp = Carbon::now()->startOfYear()->timestamp;
                    $depth = 100;
                    $chunk = 12;
                    break;
            }
        } elseif ($startDate) {
            $timestamp = Carbon::parse($startDate)->timestamp;
            $depth = 15;
        } else {
            return response()->json(['error' => 'Date range or specific date range must be provided'], 400);
        }
        
        $response = Http::withoutVerifying()->get($apiTokenData->url.$platform.'/user/posts?user_id='.$id.'&token='.$apiTokenData->token.'&depth='.$depth.'&chunk_size='.$chunk.'&oldest_timestamp='.$timestamp);

        //$cost = $response['data']['count'];

        //$this->updateApiTokenUsage($apiToken, $cost); // Exemplo de uso

        return $response;
    }

    public function fetchComments(Request $request)
    {
        $id = $request->input('id');
        $platform = $request->input('platform');
        $apiToken = $request->input('api_id');
        $limit = $request->input('comments_limit');

        $apiTokenData = ApiToken::where('id', $apiToken)->first();
        
        //$response = Http::withoutVerifying()->get($apiTokenData->url.$platform.'/post/details?code='.$id.'&token='.$apiTokenData->token.'&n_comments_to_fetch='.$limit);

        $filePath = base_path('tests/Mocks/comments.json');

        // Verifica se o arquivo existe
        if (File::exists($filePath)) {
            // Lê o conteúdo do arquivo
            $jsonContent = File::get($filePath);

            $response = json_decode($jsonContent);
        }

        //$this->updateApiTokenUsage($apiToken, 1); // Exemplo de uso

        return $response;//->json();
    }

    public function getLimit(Request $request)
    {
        $apiToken = $request->input('api_id');
        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        if ($apiTokenData) {
            $response = Http::withoutVerifying()->get($apiTokenData->url.'customer/get-used-units?date='.date_format(now(), 'Y-m-d').'&token='.$apiTokenData->token);

            return $response->json()['data'];
        }

        return response()->json(['error' => 'API Token not found'], 404);
    }
}
