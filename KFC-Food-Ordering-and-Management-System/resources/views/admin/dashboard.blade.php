<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />

  <style>
    /* ===== Grid helpers (not in admin.css) ===== */
    .grid.lg-2{grid-template-columns:2fr 1fr}
    .grid.lg-3{grid-template-columns:repeat(3,1fr)}
    .col-span-2{grid-column:span 2}

    /* ===== KPI cards ===== */
    .kpis{display:grid; gap:16px; grid-template-columns:repeat(4,1fr)}
    .kpi{background:#fff; border:1px solid var(--gray-200); border-radius:18px; padding:18px}
    .kpi-top{display:flex; align-items:flex-start; justify-content:space-between}
    .kpi-label{margin:0; color:var(--gray-600); font-weight:600; font-size:13px}
    .kpi-value{margin:.25rem 0 0; font-size:28px; font-weight:800; color:var(--gray-900)}
    .kpi-icn{display:grid; place-items:center; width:40px; height:40px; border-radius:12px; background:#fee2e2; color:var(--brand-700); font-weight:700}
    .kpi-sub{margin:8px 0 0; color:var(--gray-500); font-size:12px}

    /* ===== Charts (CSS-only placeholders) ===== */
    .chart{margin-top:6px}
    .chart-lines{
      position:relative; height:260px;
      background:linear-gradient(#fff, #fff),
        repeating-linear-gradient(0deg,#f3f4f6,#f3f4f6 1px,#fff 1px,#fff 28px);
      border-radius:12px; border:1px solid var(--gray-200)
    }
    .line-bg{position:absolute; inset:0}
    .line{
      position:absolute; inset:0 8px; display:grid; grid-template-columns:repeat(7,1fr);
      align-items:flex-end; gap:8px;
    }
    .line.orders{ --c:#2563eb; }
    .line.revenue{ --c:#dc2626; }
    /* dotted “points” to hint the series; controlled via --p1..--p7 heights */
    .line.orders::before,
    .line.revenue::before{
      content:""; position:absolute; inset:0; background:
        radial-gradient(var(--c) 4px, transparent 5px) calc(14.28%*0) calc(100% - var(--p1)),
        radial-gradient(var(--c) 4px, transparent 5px) calc(14.28%*1) calc(100% - var(--p2)),
        radial-gradient(var(--c) 4px, transparent 5px) calc(14.28%*2) calc(100% - var(--p3)),
        radial-gradient(var(--c) 4px, transparent 5px) calc(14.28%*3) calc(100% - var(--p4)),
        radial-gradient(var(--c) 4px, transparent 5px) calc(14.28%*4) calc(100% - var(--p5)),
        radial-gradient(var(--c) 4px, transparent 5px) calc(14.28%*5) calc(100% - var(--p6)),
        radial-gradient(var(--c) 4px, transparent 5px) calc(14.28%*6) calc(100% - var(--p7));
      pointer-events:none;
    }
    .chart-legend{
      position:absolute; left:10px; bottom:10px; display:flex; align-items:center; gap:14px;
      background:#fff; padding:6px 10px; border:1px solid var(--gray-200); border-radius:10px
    }
    .legend-dot{display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; vertical-align:middle}
    .legend-dot.blue{background:#2563eb}
    .legend-dot.red{background:#dc2626}

    /* ===== Bar chart ===== */
    .chart.chart-bars{
      display:flex; gap:12px; align-items:stretch; height:260px;
      padding:12px 10px 8px; border:1px solid var(--gray-200); border-radius:12px;
    }
    .chart.chart-bars .bar{display:grid; grid-template-rows:1fr auto; min-width:72px; flex:1}
    .chart.chart-bars .bar-plot{display:flex; align-items:flex-end; height:100%}
    .chart.chart-bars .bar-fill{
      width:100%; margin-top:auto; border-radius:14px; background:linear-gradient(#f87171,#dc2626);
      transition:height .45s ease;
    }
    .chart.chart-bars .bar-label{
      margin-top:8px; font-size:12px; text-align:center; line-height:1.2; color:var(--gray-700); min-height:2.4em;
    }

    /* ===== Responsive tweaks for dashboard widgets ===== */
    @media (max-width: 1024px){ .grid.lg-2{grid-template-columns:1fr} .grid.lg-3{grid-template-columns:1fr} .col-span-2{grid-column:span 1} .kpis{grid-template-columns:repeat(2,1fr)} }
  </style>
</head>
<body>
  <div class="layout">

    <!-- Sidebar  -->
    <aside class="sidebar" aria-label="Admin navigation">
      <div class="brand">
  <div class="brand-logo-text">KFC <span class="brand-sub">Admin</span></div>
</div>

      <nav class="side-nav">
          <a class="nav-link active" href="{{ route('admin.dashboard') }}">Dashboard</a>
          <a class="nav-link" href="{{ route('admin.menu') }}">Menu Items</a>
          <a class="nav-link" href="{{ route('admin.orders') }}">Orders</a>
          <a class="nav-link" href="{{ route('admin.reviews') }}">Reviews</a>
          <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
      </nav>

    </aside>

    <!-- Main -->
    <main class="main">
      
      <!-- Content -->
      <div class="container">

        <!-- KPIs -->
        <section class="kpis">
          <article class="kpi">
            <div class="kpi-top">
              <div>
                <p class="kpi-label">Orders Today</p>
                <p class="kpi-value">{{ $ordersToday }}</p>
              </div>
              <div class="kpi-icn">🧾</div>
            </div>
            <p class="kpi-sub">
               {{-- Example: compare today vs yesterday --}}
               @php
               $yesterdayOrders = $yesterdayOrders ?? 0;
               $diff = $ordersToday - $yesterdayOrders;
               @endphp
               {{ $diff >= 0 ? '+' . $diff : $diff }} vs yesterday
            </p>
          </article>

          <article class="kpi">
            <div class="kpi-top">
              <div>
                <p class="kpi-label">Active Orders</p>
                <p class="kpi-value">{{ $activeOrders }}</p>
              </div>
              <div class="kpi-icn">⏱️</div>
            </div>
            <p class="kpi-sub">
               {{-- Show breakdown --}}
     {{ $receivedCount ?? 0 }} Received Order • {{ $preparingCount ?? 0 }} Preparing 
            </p>
          </article>

          <article class="kpi">
            <div class="kpi-top">
              <div>
                <p class="kpi-label">Revenue (RM)</p>
                <p class="kpi-value">{{ $revenueToday }}</p>
              </div>
              <div class="kpi-icn">💵</div>
            </div>
            <p class="kpi-sub">
              {{-- Average order value --}}
      Avg order RM {{ $avgOrderValue ?? 0 }}
            </p>
          </article>

          <article class="kpi">
            <div class="kpi-top">
              <div>
                <p class="kpi-label">Avg. Prep Time</p>
                <p class="kpi-value"> {{ $avgPrepTime ? $avgPrepTime . 'm' : 'N/A' }}</p>
              </div>
              <div class="kpi-icn">⏳</div>
            </div>
            <p class="kpi-sub">Target ≤ 12m</p>
          </article>
        </section>

       
          <!-- Charts (static placeholders) -->
        <section class="grid lg-2">
           <article class="card">
              <div class="card-head">
                <h3>Orders & Revenue (This Week)</h3>
                <small class="muted">{{ $weekRange ?? 'Mon–Sun' }}</small>
              </div>

              <div class="chart">
                <canvas id="ordersRevenueChart" height="120"></canvas>
              </div>

              {{-- Raw JSON payload for the chart (editor won't lint this) --}}
              <script type="application/json" id="trend-json">
                {!! json_encode($trend ?? [
                    'labels'  => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                    'orders'  => [0,0,0,0,0,0,0],
                    'revenue' => [0,0,0,0,0,0,0],
                ], JSON_UNESCAPED_UNICODE) !!}
              </script>
            </article>




          <!-- Top Item -->
          <article class="card">
            <div class="card-head">
              <h3>Top Items</h3>
              <small class="muted">Last 7 days</small>
            </div>

            <div class="chart chart-bars" id="topItemsChart">
             @php $max = max($topItems->pluck('sold')->toArray() ?: [1]); @endphp

              <div class="chart chart-bars" id="topItemsChart">
                @foreach($topItems as $item)
                  @php $h = round(($item->sold / $max) * 100, 2); @endphp
                  <div class="bar">
                    <div class="bar-plot">
                      <div class="bar-fill" style="height: <?php echo $h; ?>%;"></div>
                    </div>
                    <div class="bar-label">{{ $item->name }} (×{{ $item->sold }})</div>
                  </div>
                @endforeach
              </div>

            </div>
          </article>

        </section>


        <!-- Tables -->
       <article class="card col-span-2">
  <div class="card-head between">
    <h3>Recent Orders</h3>
    {{-- no orders page yet, so just disable link --}}
    <a class="btn btn-ghost" href="{{ route('admin.orders') }}">View all</a>
  </div>

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Items</th>
          <th class="right">Total (RM)</th>
          <th>Status</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentOrders as $order)
          <tr>
            <td class="mono">#{{ $order->id }}</td>
            <td>{{ $order->user->name ?? 'N/A' }}</td>
            <td>{{ (int) $order->order_details_sum_quantity }}</td>
            <td class="right">{{ number_format($order->total_amount, 2) }}</td>
            <td>
              @php
                $statusClass = match($order->status) {
                  \App\Models\Order::RECEIVED   => 'badge-amber',
                  \App\Models\Order::PREPARING => 'badge-sky',
                  \App\Models\Order::COMPLETED => 'badge-emerald',
                  default                      => 'badge'
                };
              @endphp
              <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
            </td>
            <td>{{ $order->created_at->format('H:i') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-gray-500">No recent orders found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>

          <article class="card">
  <div class="card-head between">
    <h3>Latest Reviews</h3>
    <a class="btn btn-ghost" href="{{ route('admin.reviews') }}">View all</a>
  </div>
  <ul class="reviews">
    @forelse($latestReviews as $review)
      <li class="review">
        <div class="review-head">
          <span class="reviewer">{{ $review->user->name ?? 'Unknown' }}</span>
          <span class="muted">{{ $review->created_at->diffForHumans() }}</span>
        </div>
        <div class="stars" aria-label="{{ $review->rating }} stars">
          {{ str_repeat('★',$review->rating) }}{{ str_repeat('☆',5 - $review->rating) }}
        </div>
        <p class="review-text">{{ $review->comment }}</p>
      </li>
    @empty
      <li class="muted">No reviews found.</li>
    @endforelse
  </ul>
</article>


      </div>
    </main>
  </div>

  <!-- Optional tiny JS for mobile sidebar toggle (no framework) -->
  <script>
    const toggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
      toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
      });
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const payloadEl = document.getElementById('trend-json');
    if (!payloadEl) { console.error('trend-json not found'); return; }

    let payload;
    try { payload = JSON.parse(payloadEl.textContent || '{}'); }
    catch (e) { console.error('Invalid trend JSON', e); return; }

    const canvas = document.getElementById('ordersRevenueChart');
    if (!canvas) { console.error('ordersRevenueChart not found'); return; }

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
      data: {
        labels: payload.labels || [],
        datasets: [
          { type: 'bar',  label: 'Orders',       data: payload.orders  || [], borderWidth: 1 },
          { type: 'line', label: 'Revenue (RM)', data: payload.revenue || [], yAxisID: 'y1', tension: 0.3 }
        ]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
          y:  { beginAtZero: true, title: { display: true, text: 'Orders' } },
          y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Revenue (RM)' }, grid: { drawOnChartArea: false } }
        }
      }
    });
  });
</script>

</body>
</html>
