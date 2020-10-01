<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User\Recharge;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    function getUser()
    {
        $recharge_history = Recharge::where([
            ['user_id', Auth::user()->id], ['pending', 1]
        ])->get();
        foreach ($recharge_history as $recharge) {
            $date = Carbon::parse($recharge->created_at);
            $now = Carbon::now();
            if ($date->diffInDays($now) > 0) {
                $recharge->pending = 0;
                Auth::user()->balance->credits += $recharge->amount;
                Auth::user()->balance->pending_credits -= $recharge->amount;
                $recharge->save();
            }
            Auth::user()->balance->save();
        }
        return new UserResource(Auth::user());
    }
    //
    function create(UserRequest $request)
    {
        $user = new User($request->validated());
        $user->company_id = Auth::user()->company_id;
        $user->save();
        return new UserResource($user);
    }

    function update(UserRequest $request, $id)
    {
        $user = User::with('company')->where([
            ['id', $id], ['company_id', Auth::user()->company_id]
        ])->firstOrFail();
        $user->fill($request->validated());
        $user->save();
        return new UserResource($user);
    }

    function profileEdit(Request $request)
    {
        $valid_data = $request->validate([
            'name' => 'string',
            'email' => 'email',
            'password' => 'string|min:8|max:24'
        ]);
        Auth::user()->fill($valid_data);
        Auth::user()->save();
        return new UserResource(Auth::user());
    }

    function all()
    {
        $per_page = \Request::get('per_page') ?: 20;
        $per_page = in_array($per_page, [10,20,30]) ? $per_page : 20;
        $users = User::where([
            ['company_id', '=', Auth::user()->company_id],
            ['permission', '!=', 1],
        ])->paginate($per_page);
        return UserResource::collection($users);
    }

    function view($id)
    {
        return new UserResource(User::findOrFail($id));
    }
}
