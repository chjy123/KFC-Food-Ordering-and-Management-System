<?php
#author’s name： Lim Jing Min
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
       
        $monthStr = $request->query('month', now()->format('Y-m'));

        try {
            $month = Carbon::createFromFormat('Y-m', $monthStr);
        } catch (\Throwable $e) {
            $month = now();
            $monthStr = $month->format('Y-m');
        }

        $from = $month->copy()->startOfMonth();
        $to   = $month->copy()->endOfMonth();

        
        $daily = Order::query()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as orders, COALESCE(SUM(total_amount),0) as revenue')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $labels  = [];
        $orders  = [];
        $revenue = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $labels[]  = $d->format('j M');                   
            $orders[]  = (int)   ($daily[$key]->orders  ?? 0);
            $revenue[] = (float) ($daily[$key]->revenue ?? 0);
        }

        
        $top = OrderDetail::query()
            ->select('food_id', DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->with('food:id,name')
            ->groupBy('food_id')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        $topLabels = $top->map(fn ($r) => $r->food->name ?? '—')->values();
        $topQty    = $top->pluck('qty')->map(fn ($q) => (int)$q)->values();

        return view('admin.reports', [
            'monthStr'   => $monthStr,
            'monthTitle' => $month->format('F Y'),
            'labels'     => $labels,
            'orders'     => $orders,
            'revenue'    => $revenue,
            'topLabels'  => $topLabels,
            'topQty'     => $topQty,
        ]);
    }
}
