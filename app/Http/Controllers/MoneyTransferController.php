<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;

class MoneyTransferController extends Controller
{
    public function transfer(Request $request, $sender_account_id)
    {
        $user = Auth::user(); 

        $validated = $request->validate([
            'receiver_account_number' => 'required|exists:accounts,account_number',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:transaction_categories,id',
        ]);
    
        $sender = Account::findOrFail($sender_account_id);
        $receiver = Account::where('account_number', $validated['receiver_account_number'])->firstOrFail();
    
        if ($sender->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        if ($sender->currency_id !== $receiver->currency_id) {
            return response()->json(['message' => 'Currency mismatch'], 400);
        }
    
        if ($sender->balance < $validated['amount']) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }
    
        try {
            
            $transaction = \DB::transaction(function () use ($sender, $receiver, $validated) {
                $sender->balance -= $validated['amount'];
                $sender->save();
    
                $receiver->balance += $validated['amount'];
                $receiver->save();
    
                return Transaction::create([
                    'sender_account' => $sender->id,
                    'receiver_account' => $receiver->id,
                    'amount' => $validated['amount'],
                    'date' => now(),
                    'description' => $validated['description'],
                    'status' => 'successful',
                    'category_id' => $validated['category_id'], 
                ]);
            });
    
            return response()->json([
                'message' => 'Transfer successful',
                'transaction' => new TransactionResource($transaction)
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Transfer failed', 'error' => $e->getMessage()], 500);
        }

    }
}
