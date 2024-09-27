<?php

namespace App\Http\Services;

use App\Http\Controllers\ApiTokenController;
use App\Models\ApiToken;
use App\Models\MainBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use DateTime;
use Log;

class FetchService {
    private function updateApiTokenUsage($id, $usedAmount)
    {
        $api = new ApiTokenController();
        $request = Request::create('/', 'POST', ['id' => $id]);
        $api->restartLimit($request);

        $apiToken = ApiToken::where('id', $id)->first();
        if ($apiToken) {
            $apiToken->limit_used += $usedAmount;
            $apiToken->last_used = date_format(now(), 'Y-m-d H:i:s');
            $apiToken->save();
        }
    }
    public function FetchLikes($post_id, $apiToken, $platform) {
        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        $cost = 1;

        if($apiTokenData->name == 'Hiker' && $platform == 'instagram') {
            $response = Http::withoutVerifying()->get($apiTokenData->url.'media/likers?user_id='.$post_id.'&access_key='.$apiTokenData->token);
            $json = (object) json_decode($response);
        }

        $this->updateApiTokenUsage($apiToken, $cost);
        return $json;
    }

    public function FetchProfile($id, $apiToken, $platform) {
        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        $cost = 1;

        if($apiTokenData->name == 'Hiker' && $platform == 'instagram') {
            $response = Http::withoutVerifying()->get($apiTokenData->url.'user/by/username?username='.$id.'&access_key='.$apiTokenData->token);
            $json = (object) json_decode($response);
        }

        $this->updateApiTokenUsage($apiToken, $cost);
        return $json;
    }

    public function SearchFollowing($id, $page_id, $apiToken, $platform) {
        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        $cost = 1;

        if($apiTokenData->name == 'Hiker' && $platform == 'instagram') {
            $response = Http::withoutVerifying()->get($apiTokenData->url.'user/following?user_id='.$id.'&access_key='.$apiTokenData->token);
            if($response->successful()) {
                $json = (object) json_decode($response['response']);

                $cost++;
                $found = false;
                while(!$found) {
                    for($i = 0; $i < count($json->items); $i++) {
                        if($json->items[$i]->id == $page_id) {
                            $found = true;
                            break;
                        }
                    }

                    if($found)
                        break;

                    if($json->next_page_id == null)
                        break;

                    $response = Http::withoutVerifying()->get($apiTokenData->url.'user/following?user_id='.$id.'&page_id'.$json->next_page_id.'&access_key='.$apiTokenData->token);
                    if(!$response->successful())
                        break;

                    $cost++;

                    $json = (object) json_decode($response['response']);
                }

                $this->updateApiTokenUsage($apiToken, $cost);

                if(!$found)
                    return false;

                return true;
            }
            
        }
    }

}