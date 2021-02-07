<?php

namespace App\Http\Resources\V1;

use App\Http\Resources\V1\Thread\CommentCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'body'  => $this->body,
            'comments' => new CommentCollection(
                $this->whenLoaded('comments')
            ),
        ];
    }
}
