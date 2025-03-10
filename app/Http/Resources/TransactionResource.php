<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\TransactionCategoryResource;

//use App\Http\Resources\Cate

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->receiver_account){
            return [
                'id' => $this->id,
                'sender_account'=> new AccountResource($this->sender),
                'sender' => new UserResource($this->sender->user),
                'receiver' => new UserResource($this->receiver->user),
                'receiver_account_number' => $this->receiver->account_number,
                'category' => new TransactionCategoryResource($this->transactionCategory),
                'date' => $this->date,
                'amount' => $this->amount,
                'description' => $this->description,
                'status' => $this->status,
                'scope' => $this->scope,
            ];
        }
        else{
            return [
                'id' => $this->id,
                'sender' => new UserResource($this->sender->user),
                'sender_account'=> new AccountResource($this->sender),
                'receiver_account_number' => $this->receiver_account_number,
                'category' => new TransactionCategoryResource($this->transactionCategory),
                'date' => $this->date,
                'amount' => $this->amount,
                'description' => $this->description,
                'status' => $this->status,
                'scope' => $this->scope,
            ];
        }

        
    }
}
