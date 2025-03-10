<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;

class UserController extends Controller
{

    public function profile()
    {
        $user = Auth::user();
        
        return new UserResource($user);
    }

    public function getAccounts()
    {
        $user = Auth::user();

        $accounts = Account::where('owner_id', $user->id)->get();

        return response()->json([
            'accounts' => AccountResource::collection($accounts)
        ]);

    }
    
    public function getAccountTransactions(Account $account)
    {
        $user = Auth::user();

        if ($account->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sentTransactions = $account-> transaction_senders()->get();
        $receivedTransactions = $account-> transaction_receivers()->get();
    
        $allTransactions = $sentTransactions->merge($receivedTransactions);
        $allTransactions = $allTransactions->sortBy('created_at');
        if ($allTransactions->isEmpty()) {
            return response()->json(['message' => 'No transactions found'], 200);
        }

        return response()->json([
            'transactions' => TransactionResource::collection($allTransactions)
        ]);

    }

}