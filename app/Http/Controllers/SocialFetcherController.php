<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ApiToken;

class SocialFetcherController extends Controller
{
    private function updateApiTokenUsage($id, $usedAmount)
    {
        $apiToken = ApiToken::where('id', $id)->first();
        if ($apiToken) {
            $apiToken->limit_used += $usedAmount;
            $apiToken->last_used = date_format(now(), 'Y-m-d H:i:s');
            $apiToken->save();
        }
    }

    public function fetchProfile(Request $request)
    {
        $id = $request->input('id');
        $platform = $request->input('platform');
        $apiToken = $request->input('api_id');
        
        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        $response = Http::withoutVerifying()->get($apiTokenData->url.$platform.'/user/detailed-info?username='.$id.'&token='.$apiTokenData->token);

        $cost = 1;
        if($platform == 'instagram')
            $cost = 10;

        $this->updateApiTokenUsage($apiToken, $cost);

        return $response->json();
    }

    public function fetchPosts(Request $request)
    {
        $url = $request->input('url');
        $apiToken = $request->input('api_id');
        $dateRange = $request->input('date_range'); // Pode ser 'today', 'week', 'month', 'year'
        $startDate = $request->input('start_date'); // Data de início para intervalos específicos (formato: 'YYYY-MM-DD')
        $endDate = $request->input('end_date'); // Data de fim para intervalos específicos (formato: 'YYYY-MM-DD')

        // Valida se dateRange está presente e não usa start_date e end_date
        $postData = [];
        if ($dateRange) {
            $postData['date_range'] = $dateRange;
        } elseif ($startDate && $endDate) {
            // Usa intervalo de datas se disponível
            $postData['start_date'] = $startDate;
            $postData['end_date'] = $endDate;
        } else {
            return response()->json(['error' => 'Date range or specific date range must be provided'], 400);
        }
        
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
        ])->post($url, [
            'api_token' => $apiToken,
            'date_range' => $dateRange,
        ]);

        $this->updateApiTokenUsage($apiToken, 1); // Exemplo de uso

        return $response->json();
    }

    public function fetchComments(Request $request)
    {
        $url = $request->input('url');
        $apiToken = $request->input('api_id');
        $postId = $request->input('post_id');
        $commentsLimit = $request->input('comments_limit');
        
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
        ])->post($url, [
            'api_token' => $apiToken,
            'post_id' => $postId,
            'comments_limit' => $commentsLimit,
        ]);

        $this->updateApiTokenUsage($apiToken, 1); // Exemplo de uso

        return $response->json();
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
