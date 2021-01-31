<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ThreadStoreRequest;
use App\Http\Requests\V1\ThreadUpdateRequest;
use App\Http\Resources\V1\ThreadCollection;
use App\Http\Resources\V1\ThreadResource;
use App\Models\Thread;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResource
     */
    public function index()
    {
        $threads = Thread::all();

        return new ThreadCollection($threads);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ThreadStoreRequest $request
     * @return JsonResource
     */
    public function store(ThreadStoreRequest $request)
    {
        $thread = Thread::create($request->validated());

        return new ThreadResource($thread);
    }

    /**
     * Display the specified resource.
     *
     * @param  Thread $thread
     * @return JsonResource
     */
    public function show(Thread $thread)
    {
        return new ThreadResource($thread);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ThreadUpdateRequest $request
     * @param  Thread $thread
     * @return JsonResource
     */
    public function update(ThreadUpdateRequest $request, Thread $thread)
    {
        $data = $request->validated();

        $thread->fill($data);
        $thread->save();

        return new ThreadResource($thread);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Thread $thread
     * @return Response
     */
    public function destroy(Thread $thread)
    {
        $thread->delete();

        return response()->noContent();
    }
}
