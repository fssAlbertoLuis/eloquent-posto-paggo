<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\PurchaseResource;
use App\Models\Company\Fuel;
use App\Models\User\Purchase;
use App\Models\User\Recharge;
use App\Rules\Money;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserHistoryController extends Controller
{
    function rechargeCredits(Request $request)
    {
        $valid_data = $request->validate([
            'amount' => ['required', 'not_in:0', new Money()]
        ]);

        //if payment api succeeds
        if (true) {
            $recharge = new Recharge($valid_data);
            $recharge->user_id = Auth::user()->id;
            $recharge->save();
            Auth::user()->balance->pending_credits += $recharge->amount;
            Auth::user()->balance->save();
            return response()->json(true);
        }
        return response()->json(false);
    }

    function purchase(Request $request)
    {
        $valid_data = $request->validate([
            'amount' => ['required', 'numeric', new Money],
            'company_id' => 'required|exists:companies,id',
            'fuel_id' => 'required|numeric',
        ]);
        $fuel = Fuel::where([
            ['id', $valid_data['fuel_id']], ['company_id', $valid_data['company_id']]
        ])->first();
        if ($fuel) {
            $purchase = new Purchase($valid_data);
            $purchase->fuel_name = $fuel->name;
            $purchase->fuel_cost_price = $fuel->cost_price;
            $purchase->fuel_price = $fuel->app_price;
            $user_balance = Auth::user()->balance;
            if ($purchase->amount <= $user_balance->credits) {
                $purchase->user_id = Auth::user()->id;
                DB::transaction(function () use ($purchase, $user_balance) {
                    $purchase->purchase_code = null;
                    $purchase->save();
                    $purchase->purchase_code = encrypt($purchase->id);
                    $purchase->save();
                    $user_balance->credits -= $purchase->amount;
                    $user_balance->save();
                });
                return new PurchaseResource($purchase);
            } else {
                return response()->json([
                    "message" => "Saldo insuficiente",
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Sua requisição possui erros verifique suas informações e tente novamente',
            ],400
            );
        }
    }

    function confirmPurchase(Request $request)
    {
        $valid_data = $request->validate([
            'code' => 'required'
        ]);
        try {
            $purchase_id = decrypt($valid_data['code']);
            $purchase = Purchase::where([
                ['id', $purchase_id], ['company_id', Auth::user()->company_id]
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
        } catch(DecryptException $e) {
            return response()->json([
                'Código de compra inválido'
            ], 400);
        }
    }

    function cancelPurchase(Request $request)
    {
        $valid_data = $request->validate([
            'purchase_id' => 'required'
        ]);
        $purchase = Auth::user()->purchaseHistory()->findOrFail($valid_data['purchase_id']);
        if (!$purchase->cancelled && !$purchase->confirmed) {
            $purchase->cancelled = 1;
            $purchase->purchase_code = null;
            $purchase->save();
            Auth::user()->balance->credits += $purchase->amount;
            Auth::user()->balance->save();
            return response()->json(true);
        } else {
            return response()->json(['message' => 'Código inválido'], 400);
        }
    }

    function purchaseList()
    {
        $purchases = Purchase::where('user_id', Auth::user()->id)->paginate(15);
        return PurchaseResource::collection($purchases);
    }

    function rechargeList(Request $request)
    {
        $recharges = Recharge::where('user_id', Auth::user()->id)->paginate(15);
    }

    function purchaseGet($id)
    {
        $purchase = Purchase::where([
            ['id', $id], ['user_id', Auth::user()->id]
        ])->firstOrFail();
        return new PurchaseResource($purchase);
    }
}
