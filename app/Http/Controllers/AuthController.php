<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Operator;
use Illuminate\Support\Facades\Validator;
use Log;
use Illuminate\Support\Facades\Mail;
use Password;

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
        // Revoke the token if using token-based authentication
        if (Auth::guard('weboperator')->check()) {
            Auth::guard('weboperator')->user()->tokens()->delete();
        } else if (Auth::check()) {
            $request->user()->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function forgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $email = $request->only('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = Operator::where('email', $email)->first();
            
            if(!$user)
                return response()->json(['message' => 'Invalid email'], 401);
        }

       
        $token =  Password::createToken($user);

        $resetLink = 'https://aibnet.online?mode=recovery&email='.$email['email'].'&token='.$token;

        $detalhes = [
            'title' => 'Recuperação de senha',
            'body' => 'Olá, '.$user->name.'!
            
                        Recebemos uma solicitação para redefinir a senha associada ao seu e-mail. Se você não fez essa solicitação, por favor, entre em contato. Caso contrário, clique no link abaixo para redefinir sua senha:

                        '.$resetLink.'

                        Este link de redefinição de senha é válido por 5 minutos. Após esse período, você precisará solicitar um novo link de redefinição de senha.

                        Se você tiver qualquer dúvida ou enfrentar problemas, entre em contato com nosso suporte.

                        Obrigado por usar nossos serviços!

                        Atenciosamente,
                        Equipe de Suporte
                        AIBNet'
        ];

        Mail::to($email['email'])->send(new ContactEmail($detalhes, 'Recuperação de senha'));

        return response()->json(['message'=> 'Email send'], 200);
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|string',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $email = $request->only('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = Operator::where('email', $email)->first();
            
            if(!$user)
                return response()->json(['message' => 'Invalid email'], 401);
        }

        $user->password = Hash::make($request->only('password'));

        return response()->json(['message'=> 'Password changed'], 200);
    }

    public function checkToken(Request $request) {
        // Verifica se o token pertence ao guard padrão
        $user = $request->user(); // Isso usa o guard padrão, que geralmente é 'web'
        if ($user) {
            if($user instanceof Operator) {
                return response()->json([
                    'valid' => true,
                    'is_operator' => true,
                    'user' => $user
                ], 200);
            }

            return response()->json([
                'valid' => true,
                'account_id' => $user->account_id,
                'is_operator' => false,
                'user' => $user
            ], 200);
        }

        // Caso nenhum usuário seja autenticado
        return response()->json(['valid' => false], 401);
    }
}
