<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Thread;

use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function getMultipleCommentsStructure() : array
    {
        return [
            'data' => [
                '*' => [
                    'id',
                    'body',
                ],
            ],
        ];
    }

    protected function getSingleCommentStructure() : array
    {
        return [
            'data' => [
                'id',
                'body',
            ],
        ];
    }

    protected function createThreadWithCommentsAndGetFirstOne()
    {
        $threadModel = Thread::factory()->has(
            Comment::factory()->count(rand(1, 10))
        )->create();

        $commentModel = $threadModel
            ->comments()
            ->inRandomOrder()
            ->firstOrFail();

        return [
            $threadModel,
            $commentModel,
        ];
    }

    /** @test */
    public function list_all_comments_in_thread()
    {
        list($threadModel, $commentModel) =
            $this->createThreadWithCommentsAndGetFirstOne();

        $commentModels = $threadModel->comments->map(function($model) {
            return $model->only([
                'id',
                'body',
            ]);
        })->toArray();

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$threadModel->id}/comments"
        );

        $response->assertStatus(200);

        $response->assertJsonStructure(
            $this->getMultipleCommentsStructure()
        );

        $response->assertJson([
            'data' => $commentModels,
        ]);
    }

    /** @test */
    public function list_all_comments_in_missing_thread()
    {
        $thread_id = rand(1, 10);

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$thread_id}/comments"
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function list_all_comments_in_empty_thread()
    {
        $threadModel = Thread::factory()->create();

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$threadModel->id}/comments"
        );

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [],
        ]);
    }

    /** @test */
    public function create_new_comment_in_thread()
    {
        $threadModel = Thread::factory()->create();

        $requestData = Comment::factory()->make()->only([
            'body',
        ]);

        $response = $this->json(
            'POST',
            "/api/v1/threads/{$threadModel->id}/comments",
            $requestData
        );

        $response->assertStatus(201);

        $response->assertJsonStructure(
            $this->getSingleCommentStructure()
        );

        $response->assertJson([
            'data' => $requestData,
        ]);
    }

    /** @test */
    public function get_single_comment_in_thread()
    {
        list($threadModel, $commentModel) =
            $this->createThreadWithCommentsAndGetFirstOne();

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentModel->id}"
        );

        $response->assertStatus(200);

        $response->assertJsonStructure(
            $this->getSingleCommentStructure()
        );

        $response->assertJson([
            'data' => $commentModel->only([
                'id',
                'body',
            ]),
        ]);
    }

    /** @test */
    public function get_missing_comment_in_empty_thread()
    {
        $threadModel = Thread::factory()->create();

        $commentId = rand(1, 10);

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentId}"
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function get_comment_in_wrong_thread()
    {
        list($threadModel, $commentModelUnused) =
            $this->createThreadWithCommentsAndGetFirstOne();

        list($threadModelUnused, $commentModel) =
            $this->createThreadWithCommentsAndGetFirstOne();

        $response = $this->json(
            'GET',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentModel->id}"
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function update_existing_comment_in_thread()
    {
        list($threadModel, $commentModel) =
            $this->createThreadWithCommentsAndGetFirstOne();

        $requestData = $commentModel->only([
            'body',
        ]);

        $response = $this->json(
            'PUT',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentModel->id}",
            $requestData
        );

        $response->assertStatus(200);

        $response->assertJson([
            'data' => $requestData,
        ]);

        $commentModel->refresh();

        $response->assertJson([
            'data' => $commentModel->only([
                'id',
                'body',
            ]),
        ]);
    }

    /** @test */
    public function update_existing_comment_in_wrong_thread()
    {
        list($threadModel, $commentModelUnused) =
            $this->createThreadWithCommentsAndGetFirstOne();

        list($threadModelUnused, $commentModel) =
            $this->createThreadWithCommentsAndGetFirstOne();

        $requestData = Comment::factory()->make()->only([
            'body',
        ]);

        $response = $this->json(
            'PUT',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentModel->id}",
            $requestData
        );

        $response->assertStatus(404);

        $newCommentModel = Comment::findOrFail($commentModel->id);

        $this->assertTrue($commentModel->is($newCommentModel));
    }

    /** @test */
    public function update_missing_comment_in_thread()
    {
        $threadModel = Thread::factory()->create();

        $commentId = rand(1, 100);

        $requestData = Comment::factory()->make()->only([
            'body',
        ]);

        $response = $this->json(
            'PUT',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentId}",
            $requestData
        );

        $response->assertStatus(404);

        $this->assertNull(Comment::find($commentId));
    }

    /** @test */
    public function delete_existing_comment_in_thread()
    {
        list($threadModel, $commentModel) =
            $this->createThreadWithCommentsAndGetFirstOne();

        $requestData = $commentModel->only([
            'body',
        ]);

        $response = $this->json(
            'DELETE',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentModel->id}"
        );

        $response->assertStatus(204);

        $this->assertNull(Comment::find($commentModel->id));
    }

    /** @test */
    public function delete_existing_comment_in_wrong_thread()
    {
        list($threadModel, $commentModelUnused) =
            $this->createThreadWithCommentsAndGetFirstOne();

        list($threadModelUnused, $commentModel) =
            $this->createThreadWithCommentsAndGetFirstOne();

        $response = $this->json(
            'DELETE',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentModel->id}"
        );

        $response->assertStatus(404);

        $this->assertNotNull(Comment::find($commentModel->id));
    }

    /** @test */
    public function delete_missing_comment_in_thread()
    {
        $threadModel = Thread::factory()->create();

        $commentId = rand(1, 10);

        $response = $this->json(
            'DELETE',
            "/api/v1/threads/{$threadModel->id}/comments/{$commentId}"
        );

        $response->assertStatus(404);
    }
}
