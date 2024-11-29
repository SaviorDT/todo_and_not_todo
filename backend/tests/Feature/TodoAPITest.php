<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Todo;

use Tests\Support\PassportClient;

class TodoAPITest extends TestCase
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
            'title' => 'test create todo title',
            'description' => 'test create todo description',
            'due_date' => '1999-01-01'
        ];

        $response = $this->postJson('/api/todo', $payload);

        $response->assertStatus(200)->assertJsonFragment($payload + ['completed' => 0]);

        $this->assertDatabaseHas('todos', $payload);
    }

    public function test_get() 
    {
        $record = [
            'title' => 'test get todo title',
            'description' => 'test get todo description',
            'due_date' => '1999-01-01',
            'user_id' => $this->user['id']
        ];

        $todo = Todo::create($record);

        $response = $this->getJson('/api/todo');

        $response->assertStatus(200)->assertJsonFragment($record);
    }

    public function test_update()
    {
        $record = [
            'title' => 'test update todo title',
            'description' => 'test update todo description',
            'due_date' => '1999-01-01',
            'user_id' => $this->user['id']
        ];

        $todo = Todo::create($record);

        $response = $this->patchJson("/api/todo/{$todo->id}", []);
        $response->assertStatus(200)->assertJsonFragment($record);


        $updatePayload = [
            'title' => 'new test update todo title',
            'description' => 'new test update todo description',
            'due_date' => '1999-01-02',
            'completed' => 1,
        ];
        $response = $this->patchJson("/api/todo/{$todo->id}", $updatePayload);
        $response->assertStatus(200)->assertJsonFragment($updatePayload);

        $this->assertDatabaseHas('todos', $updatePayload);
    }
}
