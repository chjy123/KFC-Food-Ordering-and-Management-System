<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['status' => [ 'nullable', Rule::in([Order::RECEIVED, Order::PREPARING, Order::COMPLETED]) ]]);

        $q = Order::with(['user:id,name', 'payment'])
            ->when($request->status, fn($qq) => $qq->where('status', $request->status))
            ->latest('created_at');

        return OrderResource::collection($q->paginate(20));
    }

    public function advance(Request $request, Order $order)
    {
        Gate::authorize('isAdmin');

        if (optional($order->payment)->payment_status !== 'Success') {
            return response()->json([
                'error' => 'PAYMENT_REQUIRED',
                'message' => 'Order can advance only when payment status is Success.'
            ], 409);
        }

        if ($order->status === Order::COMPLETED) {
            return new OrderResource($order); // already done
        }

        $now = now();
        if ($order->status === Order::RECEIVED) {
            $order->status = Order::PREPARING; $order->preparing_at = $now;
        } else {
            $order->status = Order::COMPLETED; $order->completed_at = $now;
        }
        $order->save();

        return new OrderResource($order->fresh(['user','payment']));
    }
}
