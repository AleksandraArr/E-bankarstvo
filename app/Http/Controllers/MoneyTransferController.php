<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Support\Facades\Validator;

class MoneyTransferController extends Controller
{
    public function transfer(Request $request, $sender_account_id)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'receiver_account_number' => 'required|string|regex:/^[A-Z]{3}\d{10}$/',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:transaction_categories,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validated = $validator->validated();

        $sender = Account::find($sender_account_id);

        if(!$sender){
            return response()->json(['message' => 'Account not found'], 400);
        }

        if ($sender->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($sender->account_number === $validated['receiver_account_number']) {
            return response()->json(['message' => 'Cannot transfer to the same account'], 400);
        }

        if ($sender->balance < $validated['amount']) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }

        $receiver = Account::where('account_number', $validated['receiver_account_number'])->first();
        $isExternal = is_null($receiver);
        
        if(!$isExternal && $sender->owner_id === $receiver->owner_id){
            return $this->processExchangeTransfer($sender, $validated);
        }

        $code = substr($validated['receiver_account_number'],0,3);
        if($sender->currency->name !== $code){
            return response()->json(['message' => 'Currency mismatch'], 400);
        }

       
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
                    'receiver_account_number' => $receiver->account_number,
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
            return response()->json(['message' => 'Transfer failed', 'errors' => $e->getMessage()], 500);
        }
    }

    private function processExchangeTransfer($sender, $validated){
        $receiver = Account::where('account_number', $validated['receiver_account_number'])->first();
        if (!$receiver) {
            return response()->json(['message' => 'Receiver account not found'], 404);
        }

        try {
            $exchangeRate =  Currency::fetchExchangeRate($sender->currency->name, $receiver->currency->name, $validated['amount']);
    
            if (!$exchangeRate) {
                return response()->json(['message' => 'Exchange rate not available'], 400);
            }
    
            $convertedAmount = $exchangeRate['convertedAmount'];
    
            try {
                $transaction = \DB::transaction(function () use ($sender, $receiver, $convertedAmount, $validated) {
                    $sender->balance -= $validated['amount'];
                    $sender->save();
    
                    
                    $receiver->balance += $convertedAmount;
                    $receiver->save();
    
                   
                    return Transaction::create([
                        'sender_account' => $sender->id,
                        'receiver_account' => $receiver->id,
                        'receiver_account_number' => $receiver->account_number,
                        'amount' => $validated['amount'],
                        'date' => now(),
                        'description' => $validated['description'],
                        'status' => 'successful',
                        'category_id' => $validated['category_id'],
                        'scope' => 'internal', 
                    ]);
                });
    
                return response()->json([
                    'message' => 'Exchange transfer successful',
                    'transaction' => new TransactionResource($transaction)
                ], 200);
    
            } catch (\Exception $e) {
                return response()->json(['message' => 'Exchange transfer failed', 'errors' => $e->getMessage()], 500);
            }
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching exchange rate', 'errors' => $e->getMessage()], 500);
        }
        
    }

    private function processExternalTransfer($sender, $validated)
    {
        try {
            $transaction = \DB::transaction(function () use ($sender, $validated) {
                $sender->balance -= $validated['amount'];
                $sender->save();

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
            return response()->json(['message' => 'Transfer failed', 'errors' => $e->getMessage()], 500);
        }
    }
}
