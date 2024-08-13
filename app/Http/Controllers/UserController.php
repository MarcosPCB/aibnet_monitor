<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Cria um novo usuário para uma respectiva conta.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'permission' => 'required|string',
            'account_id' => 'required|exists:account,id', // Certifica-se de que a conta existe
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userData = $request->only(['name', 'email', 'password', 'permission', 'account_id']);
        $userData['password'] = Hash::make($userData['password']); // Criptografar a senha

        $user = User::create($userData);

        return response()->json($user, 201);
    }

    /**
     * Atualiza os dados de um usuário (exceto o email).
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string|max:255',
            'password' => 'sometimes|nullable|string|min:6',
            'permission' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Atualizar os campos permitidos
        $userData = $request->only(['name', 'password', 'permission']);

        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']); // Criptografar a nova senha
        }

        $user->update($userData);

        return response()->json($user, 200);
    }

    /**
     * Apaga um usuário do banco de dados.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function get($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }
}
