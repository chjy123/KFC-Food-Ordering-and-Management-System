<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $today     = now()->toDateString();
        $weekStart = now()->copy()->startOfWeek(); // Monday 00:00:00
        $weekEnd   = now()->copy()->endOfWeek();   // Sunday 23:59:59

        // ---- KPIs ----
        $ordersToday  = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', now()->subDay())->count();



        // Active orders today (you only have Preparing as "active")
        $activeOrders = Order::whereDate('created_at', $today)
            ->where('status', Order::PREPARING)
            ->count();

        // Preparing orders today
        $preparingCount = Order::whereDate('created_at', $today)
            ->where('status', Order::PREPARING)
            ->count();

        // Revenue today from payments table (amount exists per your migrations)
        $revenueToday = Payment::whereDate('payment_date', $today)
            ->where('payment_status', 'Success')
            ->sum('amount');
        $receivedCount  = Order::where('status', 'Received')->count();

        // Average prep time (minutes) for orders completed today: completed_at - preparing_at
        $avgPrepTime = DB::table('orders')
            ->whereNotNull('preparing_at')
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', $today)
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, preparing_at, completed_at)) AS minutes')
            ->value('minutes');
        $avgPrepTime = $avgPrepTime ? round($avgPrepTime, 1) : 0;

        $avgOrderValue = $ordersToday ? round($revenueToday / max($ordersToday, 1), 2) : 0;

        
      // ---- Trend (orders count & paid revenue by day of THIS week) ----

$tz = config('app.timezone', 'Asia/Kuala_Lumpur');
$weekStart = now($tz)->startOfWeek(Carbon::MONDAY)->startOfDay();
$weekEnd   = now($tz)->endOfWeek(Carbon::SUNDAY)->endOfDay();

// Paid orders per day (based on payment_date)
$rows = \App\Models\Order::join('payments', 'payments.order_id', '=', 'orders.id')
    ->where('payments.payment_status', 'Success')
    ->whereBetween('payments.payment_date', [$weekStart, $weekEnd])
    ->selectRaw('DATE(payments.payment_date) AS day')
    ->selectRaw('COUNT(DISTINCT orders.id) AS orders')
    ->selectRaw('SUM(payments.amount) AS revenue')
    ->groupBy('day')
    ->orderBy('day')
    ->get()
    ->keyBy('day');

// Build 7 Mon–Sun buckets
$labels = $ordersSeries = $revenueSeries = [];
for ($i = 0; $i < 7; $i++) {
    $d = $weekStart->copy()->addDays($i);
    $key = $d->toDateString();
    $labels[] = $d->isoFormat('ddd D/M');  // e.g. Tue 26/8
    $row = $rows->get($key);
    $ordersSeries[]  = $row ? (int) $row->orders : 0;
    $revenueSeries[] = $row ? (float) $row->revenue : 0.0;
}

$trend = [
    'labels'  => $labels,
    'orders'  => $ordersSeries,
    'revenue' => $revenueSeries,
];


        // ---- Top items (last 7 days) ----
        $topItems = OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('foods', 'foods.id', '=', 'order_details.food_id')
            ->where('orders.created_at', '>=', Carbon::now()->subDays(7))
            ->select('foods.name', DB::raw('SUM(order_details.quantity) as sold'))
            ->groupBy('foods.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        // ---- Recent orders ----
        $recentOrders = Order::with('user')
        ->withSum('orderDetails', 'quantity')
        ->latest('created_at')
        ->limit(10)
        ->get(['id','user_id','status','total_amount','created_at']);

        // ---- Latest reviews ----
        $latestReviews = Review::with('user', 'food')
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'user_id', 'food_id', 'rating', 'comment', 'created_at']);

        return view('admin.dashboard', compact(
            'ordersToday',
            'yesterdayOrders',
            'activeOrders',
            'preparingCount',
            'revenueToday',
            'receivedCount',
            'avgOrderValue',
            'avgPrepTime',
            'trend',
            'topItems',
            'recentOrders',
            'latestReviews'
        ));
    }
}
