<?php
namespace App\Http\Resources;
#author’s name： Lim Jing Min
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'rating'  => (int) $this->rating,
            'comment' => $this->comment, 
            'user'    => ['id'=>$this->user?->id, 'name'=>$this->user?->name],
            'food'    => ['id'=>$this->food?->id, 'name'=>$this->food?->name],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
