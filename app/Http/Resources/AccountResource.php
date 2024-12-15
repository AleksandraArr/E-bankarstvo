<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Currency;

class AccountResource extends JsonResource
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
            'account_number' => $this->account_number,
            'balance' => $this->balance,
            'type' => $this->type,
            'owner' => new UserResource($this->user),
            'currency' => new CurrencyResource($this->currency),
        ];
    }
}
