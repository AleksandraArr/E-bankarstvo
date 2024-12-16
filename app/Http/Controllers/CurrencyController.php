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

        return response()->json([
            'currency' => new CurrencyResource($currency) 
        ]);
    }
}
