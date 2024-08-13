<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiTokenController extends Controller
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'doc_url' => 'required|url',
            'token' => 'required|string',
            'email' => 'required|email',
            'limit' => 'required|integer',
            'limit_type' => 'required|in:daily,weekly,monthly,yearly',
            'expires' => 'sometimes|date_format:Y-m-d H:i:s'
        ]);

        $apiToken = ApiToken::create($request->all());

        return response()->json($apiToken, 201);
    }

    public function update(Request $request, $id)
    {
        $apiToken = ApiToken::findOrFail($id);
        $data = $request->all();

        if(isset($data['last_used']))
            $data['last_used'] = date_format(date_create($data['last_used']), 'Y-m-d H:i:s');

        if(isset($data['expires']))
            $data['expires'] = date_format(date_create($data['expires']), 'Y-m-d H:i:s');

        $apiToken->update($data);

        return response()->json($apiToken, 200);
    }

    public function get($id)
    {
        $apiToken = ApiToken::findOrFail($id);

        return response()->json($apiToken, 200);
    }

    public function restartLimit($id)
    {
        $apiToken = ApiToken::findOrFail($id);
        $now = Carbon::now();

        switch ($apiToken->limit_type) {
            case 'daily':
                if ($apiToken->last_used && $now->diffInDays($apiToken->last_used) >= 1) {
                    $apiToken->update(['limit_used' => 0]);
                    return response()->json(null, 200);
                }
                break;
            case 'weekly':
                if ($apiToken->last_used && $now->diffInWeeks($apiToken->last_used) >= 1) {
                    $apiToken->update(['limit_used' => 0]);
                    return response()->json(null, 200);
                }
                break;
            case 'monthly':
                if ($apiToken->last_used && $now->diffInMonths($apiToken->last_used) >= 1) {
                    $apiToken->update(['limit_used' => 0]);
                    return response()->json(null, 200);
                }
                break;
            case 'yearly':
                if ($apiToken->last_used && $now->diffInYears($apiToken->last_used) >= 1) {
                    $apiToken->update(['limit_used' => 0]);
                    return response()->json(null, 200);
                }
                break;
        }

        return response()->json(['message' => 'Limit reset not allowed yet'], 400);
    }

    public function restartLimitForce($id)
    {
        $apiToken = ApiToken::findOrFail($id);
        $apiToken->update(['limit_used' => 0]);
        $last_used = date_format(now(), 'Y-m-d H:i:s');
        $apiToken->update(['last_used' => $last_used]);

        return response()->json(null, 200);
    }

    public function updateLimitUsed(Request $request, $id)
    {
        $apiToken = ApiToken::findOrFail($id);
        $this->validate($request, [
            'limit_used' => 'required|integer',
        ]);

        $apiToken->update(['limit_used' => $request->limit_used]);
        $last_used = date_format(now(), 'Y-m-d H:i:s');
        $apiToken->update(['last_used' => $last_used]);

        return response()->json($apiToken, 200);
    }

    public function deactivate($id)
    {
        $apiToken = ApiToken::findOrFail($id);
        $apiToken->update(['status' => false]);

        return response()->json($apiToken, 200);
    }

    public function delete($id)
    {
        $apiToken = ApiToken::findOrFail($id);
        $apiToken->delete();

        return response()->json(null, 204);
    }
}

