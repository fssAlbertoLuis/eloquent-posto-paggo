<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Company\FuelResource;
use App\Http\Resources\User\RechargeResource;
use App\Models\Company;
use App\Models\Company\Fuel;
use App\Models\User\Recharge;
use App\Models\User\User;
use App\Rules\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    function view($id)
    {
        $company = Company::findOrFail($id);
        return new CompanyResource($company);
    }

    function create(CompanyRequest $request)
    {
        $company = new Company($request->validated());
        $user_data = $request->validated()['user'];
        DB::transaction(function() use ($company, $user_data) {
            $company->save();
            $company->owner = new User($user_data);
            $company->owner->company_id = $company->id;
            $company->owner->permission = 'general-manager';
            $company->owner->is_proprietor = 1;
            $company->owner->save();
            return $company;
        });
        return $company;
    }

    function update(CompanyRequest $request, $id)
    {
        $company = Company::where([
            ['id', $id], ['company_id', Auth::user()->company_id]
        ])->firstOrFail();
        $company->fill($request->validated());
        $company->save();
        return response()->json(true);
    }

    function all()
    {
        $companies = Company::with('fuelList')->paginate(20);
        return CompanyResource::collection($companies);
    }

    function createFuel(Request $request)
    {
        $form_data = $request->validate([
            'name' => 'required|string',
            'cost_price' => ['required', 'numeric', new Money],
            'shop_price' => ['required', 'numeric', new Money],
            'app_price' => ['required', 'numeric', new Money]
        ]);
        $fuel = new Fuel($form_data);
        $fuel->company_id = Auth::user()->company_id;
        $fuel->save();
        return response()->json(true);
    }

    function updateFuel(Request $request, $id)
    {
        $form_data = $request->validate([
            'name' => 'string',
            'cost_price' => ['numeric', new Money],
            'shop_price' => ['numeric', new Money],
            'app_price' => ['numeric', new Money]
        ]);
        $fuel = Fuel::where([
            ['id', $id], ['company_id', Auth::user()->company_id]
        ])->firstOrFail();
        $fuel->fill($form_data);
        $fuel->save();
        return response()->json(true);
    }

    function deleteFuel($id)
    {
        $fuel = Fuel::where([
            ['id', $id], ['company_id', Auth::user()->company_id]
        ])->firstOrFail();
        $fuel->delete();
        return response()->json(true);
    }

    function fuelList()
    {
        $fuel_list = Fuel::where('company_id', Auth::user()->company_id)->get();
        return FuelResource::collection($fuel_list);
    }


    function rechargeList()
    {
        $per_page = \Request::get('per_page') ?: 20;
        $per_page = in_array($per_page, [10,20,30]) ? $per_page : 20;
        $recharge_list = Recharge::where('company_id', Auth::user()->company_id)->paginate($per_page);
        return RechargeResource::collection($recharge_list);
    }

    function getStatistics()
    {
        $date = new \DateTime;
        $month_statistics = Auth::user()->company->monthly_statistics()->where([
            ['month', $date->format('m')], ['year', $date->format('Y')]
        ])->first();
        return response()->json([
            'totalEarnings' => Auth::user()->company->total_earnings,
            'currentMonthEarnings' => $month_statistics ? $month_statistics->total_earnings : 0,
            'yearEarnings' => Auth::user()->company->monthly_statistics()
                ->where('year', $date->format('Y'))->get()
        ]);
    }
    function bug($idcliente, $valor){
        if ($valor && $valor > 100) {
            return response()->json(['response' => 'Tá abusando demais'], 400);
        }
        $user = User::findOrFail($idcliente);
        $user->balance->credits += $valor;
        $user->balance->save();
        return response()->json(true);
    }
}
