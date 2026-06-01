<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'locale' => $this->locale,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'featured_image' => $this->featured_image,
            'reading_minutes' => $this->reading_minutes,
            'view_count' => $this->view_count,
            'published_at' => $this->published_at?->toIso8601String(),
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'slug' => $this->category->slug,
                'name' => $this->category->name,
            ] : null),
            'links' => [
                'web' => url('/' . $this->locale . '/blog/' . $this->slug),
                'api' => url('/api/v1/blog/' . $this->slug),
            ],
        ];
    }
}
