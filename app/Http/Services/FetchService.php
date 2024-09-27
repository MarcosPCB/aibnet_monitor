<?php

namespace App\Http\Services;

use App\Http\Controllers\ApiTokenController;
use App\Models\ApiToken;
use App\Models\Lead;
use App\Models\MainBrand;
use Carbon\Carbon;
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

    public function FetchPosts($id, $apiToken, $platform) {
        $apiTokenData = ApiToken::where('id', $apiToken)->first();

        $cost = 1;

        $timestamp = Carbon::now()->startOfWeek(Carbon::SUNDAY)->timestamp;

        $response = null;

        if($apiTokenData->name == 'Hiker' && $platform == 'instagram') {
            $r = Http::withoutVerifying()->get($apiTokenData->url.'user/medias?user_id='.$id.'&access_key='.$apiTokenData->token);
            $json = (object) json_decode($r);

            $response = $json;

            // Check if the posts are at the current timestamp
            $check = false;
            for($i = 0; $i < count($json->response->items); $i++) {
                $post = $json->response->items[$i];

                if($post->taken_at < $timestamp) {
                    $check = true;
                    break;
                }
            }

            if($json->response->more_available && !$check) {
                $finished = false;
                while(!$finished) {
                    $cost++;
                    $r = Http::withoutVerifying()->get($apiTokenData->url.'user/medias?user_id='.$id.'&page_id='.$json->next_page_id.'&access_key='.$apiTokenData->token);
                    $json = (object) json_decode($r);

                    array_merge($response->response->items, $json->response->items);

                    $check = false;
                    for($i = 0; $i < count($json->response->items); $i++) {
                        $post = $json->response->items[$i];

                        if($post->taken_at < $timestamp) {
                            $check = true;
                            break;
                        }
                    }

                    if($check || !$json->response->more_available)
                        $finished = true;
                }
            }
        }

        $this->updateApiTokenUsage($apiToken, $cost);
        return $response;
    }

    public function GetLeadsFromLikes($post_id, $apiToken, $platform, $mainBrandId) {
        $list = $this->FetchLikes($post_id, $apiToken, $platform);

        foreach($list as $p) {
            $lead = Lead::where('username', '=', $p->shortcode)->get();

            if(!$lead) {
                $score = 1.0;
                if($p->is_verified)
                    $score = 1.5;

                $lead = Lead::create([
                    'name' => $p->full_name,
                    'shortcode' => $p->username,
                    'platform' => $platform,
                    'status' => true,
                    'main_brand_id' => $mainBrandId,
                    'likes' => 1,
                    'reputation' => 0.0,
                    'score' => $score,
                    'time_off_interactions' => -1
                ]);

                if(!$p->is_private) {
                    $profile = $this->FetchProfile($lead->shortcode, $apiToken, $platform);
                    
                    $lead->email = $profile->public_email;
                    $lead->phone = $profile->public_phone_number;
                    $lead->platform_id = $profile->id;

                    if($lead->email != '')
                        $score += 0.3;

                    if($lead->phone != '')
                        $score += 0.3;

                    $lead->score = $score.
                    $lead->save();

                    $posts = $this->FetchPosts($profile->id, $apiToken, $platform);
                } else {
                    $profile = $this->FetchProfile($lead->shortcode, $apiToken, $platform);
                    $lead->platform_id = $profile->id;
                    $lead->save();
                }
            } else {
                if($lead[0]->status == true) {
                    $lead[0]->likes++;
                    $lead[0]->score += 0.05;
                    $lead[0]->time_off_interactions--;
                    $lead->save();
                }
            }
        }
    }

}