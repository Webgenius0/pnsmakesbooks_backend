<?php

namespace App\Http\Resources\API\V1;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsSingleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = $request->header('Accept-Language') ?? 'en';
        return [
            'id' => $this->id,
            'title' => Helper::translateCached($this->title, $lang),
            'url' => $this->url,
            'summary' => Helper::translateCached($this->summary, $lang),
            'published_at' => Helper::translateCached($this->published_at->format('d-M-Y'), $lang),
            'author' => Helper::translateCached($this->author, $lang),
            'image_url' => $this->image_url,
            'news_type' => $this->news_type,
            'content' => Helper::translateHtmlPreserveTags($this->content, $lang),
            'user_feedback' => $this->userFeedback,
            'category_id' => $this->category_id,
            'category' => new CategoryIndexResource($this->whenLoaded('category')),
        ];
    }
}
