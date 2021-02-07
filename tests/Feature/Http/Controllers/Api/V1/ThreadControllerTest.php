<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function getMultipleThreadsStructure() : array
    {
        return [
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'body',
                ],
            ],
        ];
    }

    protected function getSingleThreadStructure() : array
    {
        return [
            'data' => [
                'id',
                'title',
                'body',
            ],
        ];
    }

    /** @test */
    public function list_all_threads()
    {
        $threadModels = Thread::factory()->count(
            rand(1, 10)
        )->create()->map(function($model) {
            return $model->only([
                'id',
                'title',
                'body',
            ]);
        })->toArray();

        $response = $this->json(
            'GET',
            '/api/v1/threads'
        );

        $response->assertStatus(200);

        $response->assertJsonStructure(
            $this->getMultipleThreadsStructure()
        );

        $response->assertJson([
            'data' => $threadModels,
        ]);
    }

    /** @test */
    public function list_all_threads_empty()
    {
        $response = $this->json(
            'GET',
            '/api/v1/threads'
        );

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [],
        ]);
    }

    /** @test */
    public function create_new_thread()
    {
        $requestData = Thread::factory()
            ->make()
            ->only([
                'title',
                'body',
            ]);

        $response = $this->json(
            'POST',
            '/api/v1/threads',
            $requestData
        );

        $response->assertStatus(201);

        $response->assertJsonStructure(
            $this->getSingleThreadStructure()
        );

        $response->assertJson([
            'data' => $requestData,
        ]);
    }

    /** @test */
    public function get_single_thread()
    {
        $threadModel = Thread::factory()->create();

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$threadModel->id}"
        );

        $response->assertStatus(200);

        $response->assertJsonStructure(
            $this->getSingleThreadStructure()
        );

        $response->assertJson([
            'data' => $threadModel->only([
                'id',
                'title',
                'body',
            ]),
        ]);
    }

    /** @test */
    public function get_missing_thread()
    {
        $threadId = rand(1, 10);

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$threadId}"
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function update_existing_thread()
    {
        $threadModel = Thread::factory()->create();

        $requestData = Thread::factory()->make()->only([
            'title',
            'body',
        ]);

        $response = $this->json(
            'PUT',
            "/api/v1/threads/{$threadModel->id}",
            $requestData
        );

        $response->assertStatus(200);

        $response->assertJson([
            'data' => $requestData,
        ]);

        $threadModel->refresh();

        $response->assertJson([
            'data' => $threadModel->only([
                'id',
                'title',
                'body',
            ]),
        ]);
    }

    /** @test */
    public function update_missing_thread()
    {
        $threadId = rand(1, 10);

        $response = $this->json(
            'PUT',
            "/api/v1/threads/{$threadId}"
        );

        $response->assertStatus(404);

        $this->assertNull(Thread::find($threadId));
    }

    /** @test */
    public function delete_existing_thread()
    {
        $threadModel = Thread::factory()->create();

        $response = $this->json(
            'DELETE',
            "/api/v1/threads/{$threadModel->id}"
        );

        $response->assertStatus(204);

        $this->assertNull(Thread::find($threadModel->id));
    }

    /** @test */
    public function delete_missing_thread()
    {
        $threadId = rand(1, 10);

        $response = $this->json(
            'DELETE',
            "/api/v1/threads/{$threadId}"
        );

        $response->assertStatus(404);
    }
}
