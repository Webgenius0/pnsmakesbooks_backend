<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'url' => $this->url,
            'summary' => $this->summary,
            'published_at' => $this->published_at->format('d-M-Y'),
            'image_url' => $this->image_url,
            'author' => $this->author
        ];
    }
}
