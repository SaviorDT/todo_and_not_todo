<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 200);
    }

    public function login(LoginRequest $request) {
        $validated = $request->validated();

        if(!Auth::attempt($validated)) {
            return response()->json(["message" => "Invalid login details."], 401);
        }

        $token_request = Request::create('oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $validated['email'],
            'password' => $validated['password'],
            'scope' => '',
        ]);

        $result = app()->handle($token_request);
        $result_arr = json_decode($result->getContent(), true);
        $result_arr["user"] = Auth::user();

        return response()->json($result_arr, 200);
    }

    public function resetPassword() {

    }

    public function refreshToken(RefreshRequest $request) {
        $validated = $request->validated();

        $token_request = Request::create('oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $validated['refresh_token'],
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'scope' => '',
        ]);

        $result = app()->handle($token_request);
        $response = json_decode($result->getContent(), true);

        return response()->json($response, 200);
    }

    public function testToken(Request $request) {
        return response()->json(auth()->user(), 200);
    }
}
