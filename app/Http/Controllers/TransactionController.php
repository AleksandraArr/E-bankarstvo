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
        $query = Transaction::where(function ($q) use ($senderId) {
            $q->where('sender_account', $senderId)
            ->orWhere('receiver_account', $senderId);
        });
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

        if ($request->has('category_id')) {
            $query->where('category_id', '=', $request->query('category_id'));
        }
        
        if ($request->has('receiver_account')) {
            if ($request->query('receiver_account') === $senderId) {
                return response()->json(['message' => 'Cannot have same receiver and sender'], 404);
            } else {
                $query->where('receiver_account', $request->query('receiver_account'));
            }
        }

        ///uplata isplata

        if ($request->has('amount_max')) {
            $query->where('amount', '<=', $request->query('amount_max'));
        }

        if ($request->has('amount_min')) {
            $query->where('amount', '>=', $request->query('amount_min'));
        }

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->query('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->query('date_to'));
        }

        if ($request->has('status')) {
            $query->where('status',  $request->query('status'));
        }

        $transactions = $query->get();
        return response()->json($transactions);
    }

}
