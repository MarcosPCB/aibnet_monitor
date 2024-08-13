<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
    // Registrar um novo operador
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:operator',
            'password' => 'required|string|min:8',
        ]);

        $operator = Operator::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'permission' => 'employee',
        ]);

        return response()->json($operator, 201);
    }

    // Atualizar senha ou nome do operador
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8',
        ]);

        $operator = Operator::findOrFail($id);

        if ($request->has('name')) {
            $operator->name = $request->name;
        }

        if ($request->has('password')) {
            $operator->password = Hash::make($request->password);
        }

        $operator->save();

        return response()->json($operator, 200);
    }

    // Editar permissão do operador
    public function permit(Request $request, $id)
    {
        $request->validate([
            'permission' => 'required|string|in:employee,admin',
        ]);

        $operator = Operator::findOrFail($id);
        $operator->permission = $request->permission;
        $operator->save();

        return response()->json($operator, 200);
    }

    // Deletar operador
    public function delete($id)
    {
        $operator = Operator::findOrFail($id);
        $operator->delete();

        return response()->json(null, 204);
    }

    public function get($id)
    {
        $operator = Operator::findOrFail($id);
        return response()->json($operator, 200);
    }
}
