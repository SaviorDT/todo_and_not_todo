<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

use Tests\Support\PassportClient;

class AuthAPITest extends TestCase
{
    use RefreshDatabase;

    protected function setup() : void {
        parent::setUp();
        PassportClient::initClient();

        $this->withHeaders([
            'Accept' => 'application/json',
        ]);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register() {
        // Check user not existed.
        $this->assertTrue(is_null(User::where('email', 'register_test')->first()));
        $this->post('/api/auth/register', [
            'name' => 'register_test',
            'email' => 'register_test',
            'password' => 'register_test'
        ])->assertStatus(200);
        $this->assertTrue(!is_null(User::where('email', 'register_test')->first()));
    }

    public function test_login_and_refresh()
    {
        User::create(['name' => 'login_test', 'email' => 'login_test', 'password' => Hash::make('login_test')]);

        // login
        $response = $this->post('/api/auth/login', [
            'email' => 'login_test',
            'password' => 'login_test'
        ]);
        
        // check token
        $this->get('/api/auth/test', [
            'Authorization' => 'Bearer ' . $response->json()["access_token"],
            ])->assertJsonFragment([
                'name' => 'login_test'
            ]);

        auth()->forgetGuards();
            
        // check refresh token
        $response2 = $this->post('/api/auth/refresh', [
            'refresh_token' => $response->json()["refresh_token"]
        ])->assertStatus(200);

        // check old token
        $this->get('/api/auth/test', [
            'Authorization' => 'Bearer ' . $response->json()["access_token"]
        ])->assertStatus(401);

        // check new token
        $this->get('/api/auth/test', [
            'Authorization' => 'Bearer ' . $response2->json()["access_token"],
        ])->assertJsonFragment([
            'name' => 'login_test'
        ]);
    }
}
