<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TransactionCategory;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Http\Resources\TransactionCategoryResource;
use App\Http\Resources\AccountResource;

class AdminController extends Controller
{
    public function showUsers()
    {
        $users = User::all();
    
        return response()->json([
            'users' => UserResource::collection($users)
        ]);
    }

    public function showAccounts()
    {
        $accounts = Account::all();
    
        return response()->json([
            'accounts' => AccountResource::collection($accounts)
        ]);
    }

    public function createUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'jmbg' => 'required|unique:users,jmbg|digits:13',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'jmbg' => $request->jmbg,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User created successfully.',
            'user' => new UserResource($user)
        ], 201);

    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'jmbg' => 'nullable|numeric',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $user->update($request->only(['jmbg', 'first_name', 'last_name', 'email', 'password']));
    
        return response()->json([
            'message' => 'User updated successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function createTransactionCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255|unique:transaction_categories,type',
            'description' => 'nullable|string|max:500',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

    
        $category = TransactionCategory::create([
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Category successfully created.',
            'category' => new TransactionCategoryResource($category)
        ], 201);
    }

    public function updateTransactionCategory(Request $request, $id)
    {
        $category = TransactionCategory::find($id);

        if(!$category){
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|max:255|unique:transaction_categories,type,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $category->update($request->only(['type', 'description']));
    
        return response()->json([
            'message' => 'Category successfully updated.',
            'category' => new TransactionCategoryResource($category),
        ], 200);
    }

    public function deleteTransactionCategory($id)
    {
        $category = TransactionCategory::find($id);

        if(!$category){
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|exists:users,id',
            'currency_id' => 'required|exists:currencies,id',
            'account_number' => 
            'required|string|unique:accounts,account_number|regex:/^[A-Z]{3}\d{10}$/',
            'type' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account = Account::create($validator->validated());

        return response()->json([
            'message' => 'Account successfully created.',
            'account' =>  new AccountResource($account)
        ], 201);
    }

    public function updateAccount(Request $request, $id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'owner_id' => 'nullable|exists:users,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'account_number' => 'nullable|string|unique:accounts,account_number,' . $account->id,
            'type' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account->update($validator->validated());

        return response()->json([
            'message' => 'Account successfully updated.',
            'account' =>  new AccountResource($account)
        ], 200);
    }
    
    public function deleteAccount($id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $account->delete();

        return response()->json(['message' => 'Account successfully deleted.'], 200);
    }
}
