<?php

namespace Tests\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

use App\Models\User;

class PassportClient
{
    public static function initClient()
    {
        if(DB::table('oauth_clients')->where('password_client', 1)->count() == 0) {
            $clientRepository = new ClientRepository();
            $client = $clientRepository->createPasswordGrantClient(
                null,
                'Only for test',
                'http://localhost'
            );
            
            config([
                'services.passport.client_id' => $client->id,
                'services.passport.client_secret' => $client->secret,
            ]);
        }
    }

    public static function generateUserToken() {
        self::initClient();

        $user = User::create([
            'name' => "normal user for testint",
            'email' => "abc@def.com",
            'password' => '$2y$04$txjdUCnX0p0N5gT3sNfWZ.SIK5tbZSQlvADGa4dre9.wvMd1ildSG', //Hash::make("password");
            'role' => 'admin'
        ]);

        $token_request = Request::create('oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => "abc@def.com",
            'password' => "password",
            'scope' => '',
        ]);

        $result = app()->handle($token_request);
        $result_arr = json_decode($result->getContent(), true);

        return ['access_token' => $result_arr["access_token"], 'user' => $user->fresh()];
    }
}