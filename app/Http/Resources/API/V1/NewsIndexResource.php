<?php

namespace App\Http\Resources\API\V1;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = $request->header('Accept-Language') ?? 'en';
        // dd($lang);
        return [
            'id' => $this->id,
            'title' => $lang === 'en' ? $this->title : Helper::translateCached($this->title, $lang),
            'published_at' => $lang === 'en' ? $this->published_at->format('d-M-Y') : Helper::translateCached($this->published_at->format('d-M-Y'), $lang),
            'author' => $lang === 'en' ? $this->author : Helper::translateCached($this->author, $lang),
            'image_url' => $this->image_url,
            'category' => $this->category
        ];
    }
}
