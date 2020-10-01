<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleClient;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciais erradas'
            ], 401);
        }
        $permission = Auth::user()->permission;
        $data = Auth::user()->createToken('Personal access token', [$permission]);
        return response()->json([
            'access_token' => $data->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $data->token->expires_at
            )->timestamp
        ]);
    }

    public function logout()
    {
        try {
            Auth::user()->token()->revoke();
            return response()->json(true);
        } catch(\Exception $e) {
            return response()->json(false, 400);
        }
    }
}
