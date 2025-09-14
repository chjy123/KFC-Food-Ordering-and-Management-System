<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function advance(Request $request, Order $order)
    {
       
        
        if (optional($order->payment)->payment_status !== 'Success') {
            return response()->json([
                'error'   => 'PAYMENT_REQUIRED',
                'message' => 'Order can advance only when payment status is Success',
            ], 409);
        }

        if ($order->status === Order::COMPLETED) {
            return $this->payload($order, 'Already completed');
        }

        $now = now();
        if ($order->status === Order::RECEIVED) {
            $order->status = Order::PREPARING;
            $order->preparing_at = $now;
        } elseif ($order->status === Order::PREPARING) {
            $order->status = Order::COMPLETED;
            $order->completed_at = $now;
        }
        $order->save();

        return $this->payload($order, "Order advanced to {$order->status}");
    }

    private function payload(Order $o, string $msg)
    {
        return response()->json([
            'id'        => $o->id,
            'status'    => $o->status,
            'timestamps'=> [
                'received_at'  => $o->received_at,
                'preparing_at' => $o->preparing_at,
                'completed_at' => $o->completed_at,
            ],
            'message'   => $msg,
        ]);
    }
}

