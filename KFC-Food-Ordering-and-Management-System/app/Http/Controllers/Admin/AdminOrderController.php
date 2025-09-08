<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Support\Bus\CommandBus;
use App\Domain\Orders\Commands\UpdateOrderStatusCommand;
use App\Domain\Orders\Exceptions\OrderAlreadyCompleted;
use App\Domain\Orders\Exceptions\PaymentNotSuccessful;
use App\Domain\Orders\Exceptions\UnsupportedOrderStatus;
use Illuminate\Support\Facades\Auth;


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


   public function updateStatus(Request $request, CommandBus $bus, Order $order)
{
    // keep your filters/pagination
    $qParams = $request->only('q','status','page');

    try {
        $updated = $bus->dispatch(new UpdateOrderStatusCommand(
            orderId: $order->id,
            actorUserId: Auth::id(),
        ));

        return redirect()->route('admin.orders', $qParams)
            ->with('success', "Order #{$updated->id} updated to {$updated->status}.");

    } catch (OrderAlreadyCompleted $e) {
        return redirect()->route('admin.orders', $qParams)
            ->with('info', $e->getMessage());

    } catch (PaymentNotSuccessful|UnsupportedOrderStatus $e) {
        return redirect()->route('admin.orders', $qParams)
            ->with('error', $e->getMessage());
    }
}
}
