<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\PurchaseResource;
use App\Models\User\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    function getPurchase($id)
    {
        $purchase = Purchase::with('user')->where([
            ['purchase_code', $id], ['company_id', Auth::user()->company_id]
        ])->firstOrFail();
        return new PurchaseResource($purchase);
    }

    function confirmPurchase($id)
    {
        $purchase = Purchase::where([
            ['purchase_code', $id], ['company_id', Auth::user()->company_id]
        ])->firstOrFail();
        if (!$purchase->cancelled && !$purchase->confirmed) {
            $purchase->purchase_code = null;
            $purchase->confirmed = true;
            $purchase->save();
            return response()->json(true);
        } else {
            return response()->json([
                'message' => 'O código já foi utilizado'
            ], 400);
        }
    }
}
