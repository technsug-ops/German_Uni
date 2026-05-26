<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'question' => $this->question,
            'answer_html' => $this->when($request->boolean('full', true), $this->answer_html),
            'answer_md' => $this->when($request->boolean('include_markdown'), $this->answer_md),
            'intent' => $this->intent,
            'answer_minutes' => $this->answer_minutes,
            'is_featured' => $this->is_featured,
            'topic' => $this->whenLoaded('topic', fn () => $this->topic ? [
                'id' => $this->topic->id,
                'slug' => $this->topic->slug,
                'name' => $this->topic->name,
                'icon' => $this->topic->icon,
            ] : null),
        ];
    }
}
