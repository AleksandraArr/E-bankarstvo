<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;


class TransactionController extends Controller
{
    public function search(Request $request, $senderId)
    {

        //senderID, je ID accounta cije se transakcije pretrazuju
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

        if ($request->has('funds') && $request->query('funds') === 'incoming' && $request->has('receiver_account')) {
            return response()->json(['message' => 'Cannot have receiver and incoming funds'], 404);
        }

        if ($request->has('funds')) {
            $funds = $request->query('funds'); 
            if ($funds === 'incoming') {
                $query->where('receiver_account',  $account->id);
            } elseif ($funds === 'outgoing') {
                $query->where('sender_account', $account->id);
            }
        }
        
        if ($request->has('receiver_account')) {
            if ($request->query('receiver_account') === $senderId) {
                return response()->json(['message' => 'Cannot have same receiver and sender'], 404);
            } else {
                $query->where('receiver_account', $request->query('receiver_account'));
            }
        }


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

    public function generatePDF($idTransaction)
    {
        $transaction = Transaction::find($idTransaction);
        $user = Auth::user();
        if(!$transaction){
            return response()->json(['message' => 'Transaction does not exists'], 400);
        }

        if($user->id !== $transaction->owner_id){
            return response()->json(['message' => 'Unauthorized'], 403); 
        }

        if ($transaction->receiver_account) {
            $formattedTransaction = [
                'title' => 'Transaction report',
                'date' => date('m/d/Y'),
                'id' => $transaction->id,
                'sender' => $transaction->sender->user->name(),
                'receiver' => $transaction->receiver->user->name(),
                'receiver_account_number' => $transaction->receiver_account_number,
                'category' => $transaction->transactionCategory->type,
                'date' => $transaction->date,
                'amount' => $transaction->amount,
                'description' => $transaction->description,
                'status' => $transaction->status,
                'scope' => $transaction->scope,
            ];
        } else {
            $formattedTransaction = [
                'title' => 'Transaction report',
                'date' => date('m/d/Y'),
                'id' => $transaction->id,
                'sender' => $transaction->sender->user->name,
                'receiver account number' => $transaction->receiver_account_number,
                'category' => $transaction->transactionCategory->name,
                'date' => $transaction->date,
                'amount' => $transaction->amount,
                'description' => $transaction->description,
                'status' => $transaction->status,
                'scope' => $transaction->scope,
            ];
        }
              
        $pdf = PDF::loadView('transactionPDF', $formattedTransaction);
       
        return $pdf->download('transactionreport.pdf');
    }

}
