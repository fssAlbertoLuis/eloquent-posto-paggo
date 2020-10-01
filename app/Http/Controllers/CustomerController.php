<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreateRequest;
use App\Http\Resources\User\RechargeResource;
use App\Http\Resources\User\UserResource;
use App\Models\Company\MonthlyStatistics;
use App\Models\User\Balance;
use App\Models\User\Recharge;
use App\Models\User\User;
use App\Notifications\RegisterSmsConfirmation;
use App\Rules\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Twilio\Exceptions\TwilioException;

class CustomerController extends Controller
{
    function create(CustomerCreateRequest $request)
    {
        $user = new User($request->validated());
        $user->otp = rand(1111, 9999);
        if ($user->save()) {
            try {
                $user->notify(new RegisterSmsConfirmation($user->otp));
            } catch (TwilioException $e) {
                die(var_dump($e->getMessage()));
            }
            $user->balance = new Balance();
            $user->balance->user_id = $user->id;
            $user->balance->save();
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return $this->respondWithCustomData([
                    'message' => 'Unauthorized'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $permission = $user->permission;
            $data = $user->createToken('Personal access token', [$permission]);

            return response()->json([
                'access_token' => $data->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $data->token->expires_at
                )->timestamp
            ]);
        } else {
            return $this->respondWithCustomData([
                'message' => 'Não foi possível realizar o cadastro.'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    function phoneVerify($code)
    {
        $user = Auth::user();
        if (!$user->phone_verified) {
            if ($user->otp === $code) {
                $user->phone_verified = true;
                $user->save();
                return response()->json(true);
            } else {
                return response()->json([
                    'message' => 'Codigo inválido'
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json([
            'message' => 'Esse telefone já foi verificado'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    function resendCode()
    {
        $user = Auth::user();
        if (!$user->phone_verified) {
            $otp = rand(1111, 9999);
            Auth::user()->notify(new RegisterSmsConfirmation($otp));
            Auth::user()->otp = $otp;
            Auth::user()->save();
            return response()->json(true);
        } else {
            return response()->json([
                'message' => 'Esse telefone já foi verificado'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    function search($search)
    {
        $customer = User::where(function ($query) use ($search) {
            $query->where('email', $search)->orWhere('phone', $search);
        })->firstOrFail();
        return new UserResource($customer);
    }

    function rechargeCredits(Request $r)
    {
        $valid_data = $r->validate([
           'customerId' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($r) {
                    return $query->where([['id', $r->get('customerId')], ['permission', 1]]);
                })
           ],
            'amount' => ['required', new Money],
        ]);
        $customer = User::findOrFail($valid_data['customerId']);
        $recharge = new Recharge($valid_data);
        $recharge->user_id = $customer->id;
        $recharge->company_id = Auth::user()->company_id;
        $recharge->vendor_id = Auth::user()->id;
        $recharge->pending = 1;
        $recharge->save();
        $customer->balance->pending_credits += $valid_data['amount'];
        $customer->balance->save();
        $date = new \DateTime;
        $monthly_statistics = Auth::user()->company->monthly_statistics()->where([
            ['month', $date->format('m')], ['year', $date->format('Y')]
        ])->first();
        Auth::user()->company->total_earnings += $recharge->amount;
        Auth::user()->company->save();
        if ($monthly_statistics) {
            $monthly_statistics->total_earnings += $recharge->amount;
            $monthly_statistics->save();
        } else {
            $data = [
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
                'total_earnings' => $recharge->amount,
            ];
            $earnings = new MonthlyStatistics($data);
            Auth::user()->company->monthly_statistics()->save($earnings);
        }
        return new RechargeResource($recharge);
    }
}
