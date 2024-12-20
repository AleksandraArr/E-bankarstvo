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
            'receiver_account_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:transaction_categories,id',
        ]);

        $sender = Account::findOrFail($sender_account_id);

        if ($sender->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($sender->balance < $validated['amount']) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }

        $code = substr($validated['receiver_account_number'],0,3);

        if($sender->currency->name !== $code){
            return response()->json(['message' => 'Currency mismatch'], 400);
        }


        $receiver = Account::where('account_number', $validated['receiver_account_number'])->first();

        $isExternal = is_null($receiver);
        if ($isExternal) {
            return $this->processExternalTransfer($sender, $validated);
        } else {
            return $this->processInternalTransfer($sender, $validated);
        }

    }

    private function processInternalTransfer($sender, $validated)
    {
        $receiver = Account::where('account_number', $validated['receiver_account_number'])->first();

        if ($sender->currency_id !== $receiver->currency_id) {
            return response()->json(['message' => 'Currency mismatch'], 400);
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
                    'scope' => 'internal',
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

    private function processExternalTransfer($sender, $validated)
    {
        try {
            $transaction = \DB::transaction(function () use ($sender, $validated) {
                $sender->balance -= $validated['amount'];
                $sender->save();


                \Log::info('Transaction data:', [
                    'sender_account' => $sender->id,
                    'receiver_account' => null,
                    'receiver_account_number' => $validated['receiver_account_number'],
                    'amount' => $validated['amount'],
                    'scope' => 'external',  // Očekuj da 'scope' bude external
                ]);
                return Transaction::create([
                    'sender_account' => $sender->id,
                    'receiver_account' => null, 
                    'receiver_account_number' => $validated['receiver_account_number'],
                    'amount' => $validated['amount'],
                    'date' => now(),
                    'description' => $validated['description'],
                    'status' => 'successful',
                    'category_id' => $validated['category_id'],
                    'scope' => 'external', 
                ]);
            });

            return response()->json([
                'message' => 'External transfer successful',
                'transaction' => new TransactionResource($transaction)
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Transfer failed', 'error' => $e->getMessage()], 500);
        }
    }
}
