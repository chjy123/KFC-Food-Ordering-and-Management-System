<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status,
            'total'         => (float) $this->total_amount,
            'received_at'   => optional($this->received_at)->toIso8601String(),
            'preparing_at'  => optional($this->preparing_at)->toIso8601String(),
            'completed_at'  => optional($this->completed_at)->toIso8601String(),
            'customer'      => [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
            ],
            'payment'       => [
                'status' => $this->payment?->payment_status ?? 'Pending',
                'paid_at'=> optional($this->payment?->payment_date)->toIso8601String(),
            ],
            'created_at'    => $this->created_at?->toIso8601String(),
            
            #author’s name： Lim Jun Hong
            
            'items'         => $this->whenLoaded('items', function () {
                return $this->items->map(function ($i) {
                    return [
                        'food' => [
                            'id'   => $i->food->id    ?? $i->food_id,
                            'name' => $i->food->name  ?? null,
                        ],
                        'quantity'   => (int) $i->quantity,
                        'unit_price' => (float) $i->unit_price,
                        'subtotal'   => (float) ($i->quantity * $i->unit_price),
                    ];
                });
            }),
        ];
    }
}
