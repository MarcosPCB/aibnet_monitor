<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Log;

class AuthController extends Controller
{
    /**
     * Faz o login do usuário.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Recupera o usuário autenticado
            $user = Auth::user();
            $account = $user->account()->first();
            $mainBrands = $account->mainBrand()->get();
            // Retorna o usuário e um token (se estiver usando tokens, por exemplo, com Passport ou Sanctum)
            return response()->json([
                'user' => $user,
                'account' => $account,
                'mainBrands' => $mainBrands,
                'isOperator' => false,
                'token' => $user->createToken('Personal Access Token')->plainTextToken
            ], 200);
        } else if (Auth::guard('weboperator')->attempt($credentials)) {
            $user = Auth::guard('weboperator')->user();
            // Retorna o usuário e um token (se estiver usando tokens, por exemplo, com Passport ou Sanctum)
            return response()->json([
                'user' => $user,
                'isOperator' => true,
                'token' => $user->createToken('Personal Access Token')->plainTextToken
            ], 200);
        }else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    /**
     * Faz o logout do usuário.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Log::info('test');
        // Revoke the token if using token-based authentication
        if (Auth::guard('weboperator')->check()) {
            Auth::guard('weboperator')->user()->tokens()->delete();
        } else if (Auth::check()) {
            $request->user()->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
