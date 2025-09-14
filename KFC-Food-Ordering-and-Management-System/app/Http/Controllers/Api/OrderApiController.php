<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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


    #author’s name： Lim Jun Hong
    public function show(Order $order)
    {
        $order->load(['items.food', 'user:id,name', 'payment']);
        return new OrderResource($order);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'items'                => ['required','array','min:1'],
            'items.*.food_id'      => ['required','integer','min:1'],
            'items.*.quantity'     => ['required','integer','min:1'],
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $menuBase = config('services.menu.base_url', 'http://127.0.0.1:8000');

        $order = DB::transaction(function () use ($data, $userId, $menuBase) {
            $order = Order::create([
                'user_id'      => $userId,
                'status'       => 'Received',
                'total_amount' => 0,
            ]);

            foreach ($data['items'] as $line) {
                $foodId   = (int) $line['food_id'];
                $quantity = (int) $line['quantity'];

                
                $resp = Http::acceptJson()
                    ->timeout(5)
                    ->get(rtrim($menuBase, '/') . "/api/v1/foods/{$foodId}");

                if ($resp->failed()) {
                    abort(422, "Food {$foodId} not found in Menu service.");
                }

                $foodJson = $resp->json();
                
                if (array_key_exists('availability', $foodJson) && $foodJson['availability'] === false) {
                    abort(422, "Food {$foodId} is unavailable.");
                }

                $unitPrice = (float) ($foodJson['price'] ?? 0);
                if ($unitPrice <= 0) {
                    abort(422, "Food {$foodId} has invalid price.");
                }

                $order->items()->create([
                    'food_id'    => $foodId,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            $order->update([
                'total_amount' => $order->items->sum(fn ($i) => $i->quantity * $i->unit_price),
            ]);

            return $order->fresh(['items.food','user:id,name','payment']);
        });

        return (new OrderResource($order))
            ->additional(['message' => 'Order created'])
            ->response()
            ->setStatusCode(201);
    }
}
