<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'rating'  => (int) $this->rating,
            'comment' => $this->comment, // already sanitized on input ideally
            'user'    => ['id'=>$this->user?->id, 'name'=>$this->user?->name],
            'food'    => ['id'=>$this->food?->id, 'name'=>$this->food?->name],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
