<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Facades\Http;

class Currency extends Model
{
    protected $fillable = ['name', 'date', 'exchange_rate'];

    public function accounts() : HasMany
    {
       return $this->hasMany(Account::class);
    }

    public static function fetchExchangeRate($fromCurrency, $toCurrency, $amount = 1)
    {
        $apiKey = '3d2f22000a644df627cbf86a29a91adc0db2550d';
        $apiUrl = "https://api.getgeoapi.com/v2/currency/convert";

        $response = Http::get($apiUrl, [
            'api_key' => $apiKey,
            'from' => $fromCurrency,
            'to' => $toCurrency,
            'amount' => $amount,
            'format' => 'json'
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['status']) && $data['status'] === 'success' && isset($data['rates'][$toCurrency])) {
                $rate = $data['rates'][$toCurrency]['rate'];
                $convertedAmount = $rate * $amount;
                $updatedDate = $data['updated_date'] ?? null;
                return [
                    'rate' => $rate,
                    'convertedAmount' => $convertedAmount,
                    'updated_date' => $updatedDate
                ];
            } else {
                throw new \Exception('Failed to fetch exchange rate.');
            }
        } else {
            throw new \Exception('Failed to fetch exchange rate.');
        }
    }

}
