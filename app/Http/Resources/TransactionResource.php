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
        return [
            'id' => $this->id,
            'sender' => new UserResource($this->sender->user),
            'receiver' => new UserResource($this->receiver->user),
            'category' => new TransactionCategoryResource($this->transactionCategory),
            'date' => $this->date,
            'amount' => $this->amount . " ".$this->sender->currency->name,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
