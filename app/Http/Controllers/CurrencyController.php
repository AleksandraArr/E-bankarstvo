<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Http\Resources\CurrencyResource;


class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            try {
                $exchangeData = Currency::fetchExchangeRate($currency->name, 'RSD');
                $currency->exchange_rate = $exchangeData['rate'];
                $currency->date = $exchangeData['updated_date'];
                $currency->save();
            } catch (\Exception $e) {
                \Log::error("Failed to update currency: {$currency->id}. Error: {$e->getMessage()}");
            }
        }
    
        return response()->json([
            'currencies' => CurrencyResource::collection($currencies)
        ]);
    }

    public function show($id)
    {
        $currency = Currency::find($id);

        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        try {
            $exchangeData = Currency::fetchExchangeRate($currency->name, 'RSD');
            $currency->rate = $exchangeData['rate'];
            $currency->date = $exchangeData['updated_date'];
            $currency->save();
        } catch (\Exception $e) {
            \Log::error("Failed to update currency: {$currency->id}. Error: {$e->getMessage()}");
        }

        return response()->json([
            'currency' => new CurrencyResource($currency) 
        ]);
    }
}
