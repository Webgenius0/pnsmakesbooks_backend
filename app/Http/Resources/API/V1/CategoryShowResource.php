<?php

namespace App\Http\Resources\API\V1;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryShowResource extends JsonResource
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
            'name' => $lang === 'en' ? $this->name : Helper::translateCached($this->name, $lang),
            'related_keywords' => $this->related_keywords,
            'image' => $this->image,
            'news' => NewsShowResource::collection($this->whenLoaded('news')),
        ];
    }
}
