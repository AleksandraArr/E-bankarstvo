<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class TransactionController extends Controller
{
    public function search(Request $request, $senderId)
    {
        $query = Transaction::query();
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $account = Account::find($senderId);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        if ($account->owner_id !== $user->id) {
            return response()->json(['message' => 'Not allowed'], 404);
        }
        
        $query->where('sender_account', $senderId)
          ->orWhere('receiver_account', $senderId);

        if ($request->has('receiver_account')) {
            $query->where('receiver_account', $request->query('receiver_account'))
                ->where('sender_account', $senderId);
        }
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        $transactions = $query->get();
        return response()->json($transactions);
    }

}
