<?php

namespace App\Http\Controllers\Api\V1\Thread;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Thread\CommentStoreRequest;
use App\Http\Requests\V1\Thread\CommentUpdateRequest;
use App\Http\Resources\V1\Thread\CommentCollection;
use App\Http\Resources\V1\Thread\CommentResource;
use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class CommentController extends Controller
{
    /**
     * CommentController constructor.
     */
    public function __construct()
    {
        /*
         * Model binding resolution logic, for the endpoint.
         */
        Route::bind('comment', function($comment, $route) {
            return Comment
                ::where('thread_id', '=', $route->parameter('thread'))
                ->findOrFail($comment);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Thread $thread
     * @return JsonResource
     */
    public function index(Thread $thread)
    {
        $comments = $thread->comments()->get();

        return new CommentCollection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CommentStoreRequest $request
     * @param  Thread $thread
     * @return JsonResource
     */
    public function store(CommentStoreRequest $request,
                            Thread $thread)
    {
        $comment = new Comment();
        $comment->fill($request->validated());
        $comment->thread_id = $thread->id;
        $comment->save();

        return new CommentResource($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  Thread $thread
     * @param  Comment $comment
     * @return JsonResource
     */
    public function show(Thread $thread, Comment $comment)
    {
        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CommentUpdateRequest $request
     * @param  Thread $thread
     * @param  Comment  $comment
     * @return JsonResource
     */
    public function update(CommentUpdateRequest $request,
                           Thread $thread,
                           Comment $comment)
    {
        $comment->fill($request->validated());

        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Thread $thread
     * @param  Comment $comment
     * @return Response
     */
    public function destroy(Thread $thread, Comment $comment)
    {
        $comment->delete();

        return response()->noContent();
    }
}
