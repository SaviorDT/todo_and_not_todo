<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class PassportClient
{
    public static function initClient()
    {
        if(!DB::table('oauth_clients')->where('password_client', 1)->count() > 0) {
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
}