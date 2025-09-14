<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    #author’s name： Lim Jing Min
    public function advance(Request $request, Order $order)
    {
        $pay = $order->payment; 
        if (! $pay || $pay->payment_status !== 'Success') {
            return response()->json([
                'error'   => 'PAYMENT_REQUIRED',
                'message' => 'Order can advance only when payment status is Success',
            ], 409);
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

            case Order::COMPLETED:
                return response()->json([
                    'error'   => 'ALREADY_COMPLETED',
                    'message' => 'Order is already completed.',
                ], 409);

            default:
                return response()->json([
                    'error'   => 'INVALID_STATE',
                    'message' => 'Unsupported current status.',
                ], 422);
        }

        $order->save();

        return response()->json([
            'id'     => $order->id,
            'status' => $order->status,
            'timestamps' => [
                'received_at'  => optional($order->received_at)->utc()?->toIso8601String(),
                'preparing_at' => optional($order->preparing_at)->utc()?->toIso8601String(),
                'completed_at' => optional($order->completed_at)->utc()?->toIso8601String(),
            ],
            'message' => $order->status === Order::PREPARING
                ? 'Order advanced to Preparing'
                : 'Order advanced to Completed',
        ], 200);
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
