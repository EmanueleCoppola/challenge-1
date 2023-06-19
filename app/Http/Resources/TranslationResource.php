<?php

namespace App\Http\Resources;

use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Translation $translation */
        $translation = $this->resource;

        return [
            'key' => $translation->key,
            'labels' => $this->labels->pluck('text', 'lang')
        ];
    }
}
