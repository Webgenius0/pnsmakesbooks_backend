<?php

namespace App\Http\Resources\API\V1;

use App\Helpers\Helper;
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
        $lang = $request->header('Accept-Language') ?? 'en';
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'url' => $this->url,
            'summary' => $lang === 'en' ? $this->summary : Helper::translateCached($this->summary, $lang),
            'published_at' => $lang === 'en' ? $this->published_at->format('d-M-Y') : Helper::translateCached($this->published_at->format('d-M-Y'), $lang),
            'image_url' => $this->image_url,
            'author' => $lang === 'en' ? $this->author : Helper::translateCached($this->author, $lang),
        ];
    }
}
