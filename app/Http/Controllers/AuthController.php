<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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
            // Retorna o usuário e um token (se estiver usando tokens, por exemplo, com Passport ou Sanctum)
            return response()->json([
                'user' => $user,
                'token' => $user->createToken('Personal Access Token')->plainTextToken
            ], 200);
        } else if (Auth::guard('weboperator')->attempt($credentials)) {
            $user = Auth::guard('weboperator')->user();
            // Retorna o usuário e um token (se estiver usando tokens, por exemplo, com Passport ou Sanctum)
            return response()->json([
                'user' => $user,
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
        // Revoke the token if using token-based authentication
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
