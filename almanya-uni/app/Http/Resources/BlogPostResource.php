<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'locale' => $this->locale,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content_html' => $this->content_html,
            'content_md' => $this->when($request->boolean('include_markdown'), $this->content_md),
            'featured_image' => $this->featured_image,
            'featured_image_caption' => $this->featured_image_caption,
            'reading_minutes' => $this->reading_minutes,
            'view_count' => $this->view_count,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'published_at' => $this->published_at?->toIso8601String(),
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'slug' => $this->category->slug,
                'name' => $this->category->name,
            ] : null),
            'author' => $this->whenLoaded('author', fn () => $this->author ? [
                'name' => $this->author->name,
            ] : null),
            'available_locales' => $this->whenLoaded('translations', fn () => $this->translations->pluck('locale')->unique()->values()),
            'links' => [
                'web' => url('/' . $this->locale . '/blog/' . $this->slug),
            ],
        ];
    }
}
