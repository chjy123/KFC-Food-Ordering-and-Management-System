<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
public function index(Request $request)
{
    $status = $request->query('status');
    $term   = trim((string) $request->query('q', ''));

    
    $isIdSearch = $term !== '' && preg_match('/^\s*#?\d+\s*$/', $term);
    $idValue    = $isIdSearch ? (int) preg_replace('/\D+/', '', $term) : null;

    $orders = \App\Models\Order::query()
        ->with([
            'user:id,name,phoneNo',
            'payment:id,order_id,payment_status,payment_method,payment_date,amount',
            'orderDetails:id,order_id,food_id,quantity,unit_price',
            'orderDetails.food:id,name',
        ])
        ->withSum('orderDetails', 'quantity')
        ->when($status, fn ($qb) => $qb->where('status', $status))
        ->when($term !== '', function ($qb) use ($isIdSearch, $idValue, $term) {
            if ($isIdSearch) {
                
                $qb->whereKey($idValue);
            } else {
                
                $qb->where(function ($sub) use ($term) {
                    $sub->whereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%")
                           ->orWhere('phoneNo', 'like', "%{$term}%");
                    });
                });
            }
        })
        ->latest('created_at')
        ->paginate(12)
        ->appends($request->only('q','status'));

    return view('admin.orders', compact('orders','status','term'));
}


   public function updateStatus(Request $request, Order $order)
{
    // preserve current filters/pagination when redirecting
    $qParams = $request->only('q','status','page');

    // Normalize payment status; pretty keeps the original casing for messages
    $payPretty = optional($order->payment)->payment_status ?? 'Pending';
    $payKey    = strtolower($payPretty); // 'pending' | 'success' | 'failed' |

    if ($order->status === Order::COMPLETED) {
        return redirect()->route('admin.orders', $qParams)
            ->with('info', "Order #{$order->id} is already Completed.");
    }

    if ($payKey !== 'success') {
        // Tailored reason
        $why = match ($payKey) {
            'pending'  => 'Payment is Pending. Order has not yet been paid for.',
            'failed'   => 'Payment Failed. The order payment was unsuccessful.',
            default    => 'Payment not successful. You can only update when payment status is Success.',
        };

        return redirect()->route('admin.orders', $qParams)
            ->with('error', "Order #{$order->id} cannot be updated: {$why}");
    }

    
    $now = now();
    switch ($order->status) {
        case Order::RECEIVED:
            $order->status       = Order::PREPARING;
            $order->preparing_at = $now;
            break;

        case Order::PREPARING:
            $order->status       = Order::COMPLETED;
            $order->completed_at = $now;
            break;

        default:
           
            return redirect()->route('admin.orders', $qParams)
                ->with('error', "Order #{$order->id} has an unsupported status.");
    }

    $order->save();

    return redirect()->route('admin.orders', $qParams)
        ->with('success', "Order #{$order->id} updated to {$order->status}.");
}

}
