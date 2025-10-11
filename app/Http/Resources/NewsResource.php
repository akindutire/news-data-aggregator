<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'category' => $this->category,
            'published_at' => $this->published_at,
            'source' => $this->source,
            'author' => $this->author,
            'content' => $this->content,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'article_remote_key' => $this->article_remote_key,
            'article_remote_source' => $this->article_remote_source,
        ];
    }
}
