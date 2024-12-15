<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getAccounts()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $accounts = Account::where('owner_id', $user->id)->get();

        return response()->json($accounts);

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
    
        if ($allTransactions->isEmpty()) {
            return response()->json(['message' => 'No transactions found'], 404);
        }
    
        return response()->json(['transactions' => $allTransactions], 200);

    }

}