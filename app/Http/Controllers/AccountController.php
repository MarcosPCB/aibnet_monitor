<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Cria uma nova conta.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'token' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'installments' => 'required|integer',
            'contract_time' => 'required|integer',
            'paid' => 'boolean',
            'contract_type' => 'required|string|max:255',
            'contract_description' => 'nullable|string',
            'contract_brands' => 'required|integer',
            'contract_brand_opponents' => 'required|integer',
            'contract_users' => 'required|integer',
            'contract_build_brand_time' => 'required|integer',
            'contract_monitored' => 'required|integer',
            'cancel_time' => 'required|integer',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account = Account::create($request->all());

        return response()->json($account, 201);
    }

    /**
     * Atualiza uma conta existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $account_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'token' => 'sometimes|required|string|max:255',
            'payment_method' => 'sometimes|required|string|max:255',
            'installments' => 'sometimes|required|integer',
            'contract_time' => 'sometimes|required|integer',
            'paid' => 'sometimes|boolean',
            'contract_type' => 'sometimes|required|string|max:255',
            'contract_description' => 'nullable|string',
            'contract_brands' => 'sometimes|required|integer',
            'contract_brand_opponents' => 'sometimes|required|integer',
            'contract_users' => 'sometimes|required|integer',
            'contract_build_brand_time' => 'sometimes|required|integer',
            'contract_monitored' => 'sometimes|required|integer',
            'cancel_time' => 'sometimes|required|integer',
            'active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account = Account::find($account_id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $account->update($request->all());

        return response()->json($account, 200);
    }

    /**
     * Cria uma nova conta e um usuário associado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createComplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|array',
            'account.name' => 'required|string|max:255',
            'account.token' => 'required|string|max:255',
            'account.payment_method' => 'required|string|max:255',
            'account.installments' => 'required|integer',
            'account.contract_time' => 'required|integer',
            'account.paid' => 'boolean',
            'account.contract_type' => 'required|string|max:255',
            'account.contract_description' => 'nullable|string',
            'account.contract_brands' => 'required|integer',
            'account.contract_brand_opponents' => 'required|integer',
            'account.contract_users' => 'required|integer',
            'account.contract_build_brand_time' => 'required|integer',
            'account.contract_monitored' => 'required|integer',
            'account.cancel_time' => 'required|integer',
            'account.active' => 'boolean',
            'user' => 'required|array',
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|string|min:6',
            'user.permission' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $accountData = $request->input('account');
        $userData = $request->input('user');

        // Cria a conta
        $account = Account::create($accountData);

        // Cria o usuário associado
        $userData['account_id'] = $account->id;
        $userData['password'] = bcrypt($userData['password']);
        $user = User::create($userData);

        return response()->json([
            'account' => $account,
            'user' => $user,
        ], 201);
    }

    public function get($id)
    {
        $account = Account::findOrFail($id);
        return response()->json($account, 200);
    }
}
