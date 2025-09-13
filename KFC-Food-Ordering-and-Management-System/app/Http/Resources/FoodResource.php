<?php
#author’s name： Yew Kai Quan
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'price'           => $this->price,
            'image_url'       => $this->image_url,
            'category'        => $this->whenLoaded('category', function () {
                return [
                    'id'   => $this->category->id,
                    'name' => $this->category->category_name,
                    'slug' => $this->category->slug,
                ];
            }),
            'reviews_count'   => $this->reviews_count ?? 0,
            'reviews_avg_rating' => round($this->reviews_avg_rating ?? 0, 1),
            'created_at'      => $this->created_at?->toDateTimeString(),
            'updated_at'      => $this->updated_at?->toDateTimeString(),
        ];
    }
}
