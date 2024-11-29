<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Progress;

use Tests\Support\PassportClient;

class ProgressAPITest extends TestCase
{
    use RefreshDatabase;

    protected function setup() : void {
        parent::setUp();

        $user_token = PassportClient::generateUserToken();
        $this->user = $user_token['user'];

        $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $user_token['access_token']
        ]);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create()
    {
        $payload = [
            'title' => 'test create progress title',
            'description' => 'test create progress description',
            'due_date' => '1999-01-01',
            'max_value' => 100
        ];

        $response = $this->postJson('/api/progress', $payload);

        $response->assertStatus(200)->assertJsonFragment($payload + ['current_value' => 0]);

        $this->assertDatabaseHas('progresses', $payload);
    }

    public function test_get() 
    {
        $record = [
            'title' => 'test get progress title',
            'description' => 'test get progress description',
            'due_date' => '1999-01-01',
            'max_value' => 100,
            'user_id' => $this->user['id']
        ];

        $progress = Progress::create($record);

        $response = $this->getJson('/api/progress');

        $response->assertStatus(200)->assertJsonFragment($record);
    }

    public function test_update()
    {
        $record = [
            'title' => 'test update progress title',
            'description' => 'test update progress description',
            'due_date' => '1999-01-01',
            'max_value' => 100,
            'user_id' => $this->user['id']
        ];

        $progress = Progress::create($record);

        $response = $this->patchJson("/api/progress/{$progress->id}", []);
        $response->assertStatus(200)->assertJsonFragment($record);


        $updatePayload = [
            'title' => 'new test update progress title',
            'description' => 'new test update progress description',
            'due_date' => '1999-01-02',
            'current_value' => 1,
        ];
        $response = $this->patchJson("/api/progress/{$progress->id}", $updatePayload);
        $response->assertStatus(200)->assertJsonFragment($updatePayload);

        $this->assertDatabaseHas('progresses', $updatePayload);
    }
}
