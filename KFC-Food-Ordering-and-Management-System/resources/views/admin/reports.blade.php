<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Reports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>

  <style>
    .container { max-width: 1200px; margin: 0 auto; padding: 16px; }
    .toolbar { display:flex; gap:10px; align-items:center; justify-content:space-between; margin-bottom:12px }
    .stack { display:grid; gap:12px }
    .grid { display:grid; gap:16px; grid-template-columns: 1fr 1fr; }
    @media (max-width: 960px){ .grid { grid-template-columns: 1fr; } }
    .card { background:#fff; border:1px solid var(--gray-200); border-radius:18px; }
    .card-head { padding:14px 16px; border-bottom:1px solid var(--gray-200); display:flex; align-items:center; justify-content:space-between }
    .card-body { padding:16px }
    .btn { border-radius:10px; padding:10px 12px; border:1px solid var(--gray-300); background:#fff; cursor:pointer; font-weight:700 }
    .btn-primary { background: var(--red-700); color:#fff; border-color: var(--red-700) }
    .select, input[type="month"] { padding:10px 12px; border:1px solid var(--gray-300); border-radius:12px; background:#fff }
    .muted { color: var(--gray-500); font-size:12px }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
    canvas { width:100%; height:380px; }
  </style>
</head>
<body>
<div class="layout">
  <!-- Sidebar (optional – keep your existing sidebar if you have one) -->
  <aside class="sidebar" aria-label="Admin navigation">
    <div class="brand">
      <div class="brand-logo-text">KFC <span class="brand-sub">Admin</span></div>
    </div>
    <nav class="side-nav">
      <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
      <a class="nav-link" href="{{ route('admin.menu') }}">Menu Items</a>
      <a class="nav-link" href="{{ route('admin.orders') }}">Orders</a>
      <a class="nav-link" href="{{ route('admin.reviews') }}">Reviews</a>
      <a class="nav-link active" href="{{ route('admin.reports') }}">Reports</a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:auto">
      @csrf
      <button type="submit" class="btn btn-ghost" style="width:100%;justify-content:center">
        <span>Log out</span>
      </button>
      <div class="muted-tiny" style="text-align:center;margin-top:6px;">
        Signed in as <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
      </div>
    </form>
  </aside>

  <main class="main">
    <div class="container">

      <div class="toolbar">
        <form method="GET" action="{{ route('admin.reports') }}" class="stack" style="grid-auto-flow:column;align-items:center">
          <label class="mono" for="month">Month</label>
          <input id="month" class="select" type="month" name="month" value="{{ $monthStr }}" />
          <button class="btn btn-primary">Apply</button>
        </form>

        <button id="btnExportPdf" class="btn">Download PDF</button>
      </div>

      <section class="stack">
        <!-- Orders & Revenue -->
        <article class="card">
          <div class="card-head">
            <strong>Orders & Revenue ({{ $monthTitle }})</strong>
            <span class="muted">Bars = Orders • Line = Revenue (RM)</span>
          </div>
          <div class="card-body">
            <canvas id="ordersRevenueChart" width="900" height="380"></canvas>
          </div>
        </article>

        <!-- Top Items -->
        <article class="card">
          <div class="card-head">
            <strong>Top Items ({{ $monthTitle }})</strong>
            <span class="muted">By quantity</span>
          </div>
          <div class="card-body">
            <canvas id="topItemsChart" width="900" height="380"></canvas>
          </div>
        </article>
      </section>

      <!-- JSON payload for JS (lint-safe) -->
      <script type="application/json" id="report-data">
        {!! json_encode([
          'monthTitle' => $monthTitle,
          'labels'     => $labels,
          'orders'     => $orders,
          'revenue'    => $revenue,
          'topLabels'  => $topLabels,
          'topQty'     => $topQty,
        ], JSON_UNESCAPED_UNICODE) !!}
      </script>

    </div>
  </main>
</div>

<!-- Chart.js + html2canvas + jsPDF (CDNs) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

<script>
(function(){
  const payload = JSON.parse(document.getElementById('report-data').textContent || '{}');

  // ===== Orders & Revenue =====
  const orCtx = document.getElementById('ordersRevenueChart').getContext('2d');
  const ordersRevenueChart = new Chart(orCtx, {
    type: 'bar',
    data: {
      labels: payload.labels || [],
      datasets: [
        {
          type: 'bar',
          label: 'Orders',
          data: payload.orders || [],
          borderWidth: 0,
          backgroundColor: 'rgba(37, 99, 235, 0.7)'
        },
        {
          type: 'line',
          label: 'Revenue (RM)',
          data: payload.revenue || [],
          yAxisID: 'y1',
          tension: 0.3,
          borderColor: 'rgba(220, 38, 38, 0.9)',
          backgroundColor: 'rgba(220, 38, 38, 0.15)',
          fill: true,
          pointRadius: 2
        }
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

  // ===== Top Items =====
  const tiCtx = document.getElementById('topItemsChart').getContext('2d');
  const topItemsChart = new Chart(tiCtx, {
    type: 'bar',
    data: {
      labels: payload.topLabels || [],
      datasets: [{
        label: 'Qty',
        data: payload.topQty || [],
        backgroundColor: 'rgba(185, 28, 28, 0.85)'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, title: { display: true, text: 'Quantity' } }
      }
    }
  });

  // ===== Download PDF (both charts) =====
   const btn = document.getElementById('btnExportPdf');
    btn?.addEventListener('click', async () => {
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF({ unit: 'mm', format: 'a4' });

      const margin = 12;
      const gap = 6;

      const pageW = pdf.internal.pageSize.getWidth();
      const pageH = pdf.internal.pageSize.getHeight();
      const innerW = pageW - margin * 2;

      let y = margin;

      // Big title
      pdf.setFont('helvetica', 'bold');
      pdf.setFontSize(14);
      pdf.text(`Monthly Report — ${payload.monthTitle || ''}`, margin, y + 4);
      y += 10;

      // Blocks to print (chart + subtitle)
      const blocks = [
        { el: document.getElementById('ordersRevenueChart'), title: `Orders & Revenue (${payload.monthTitle || ''})` },
        { el: document.getElementById('topItemsChart'),      title: `Top Items (${payload.monthTitle || ''})` },
      ];

      // Available vertical space for both charts
      const availableH = pageH - y - margin;
      const blockH = (availableH - gap) / 2; // each chart height

      for (const blk of blocks) {
        // subtitle
        pdf.setFont('helvetica', 'bold');
        pdf.setFontSize(12);
        pdf.text(blk.title, margin, y + 5);
        y += 8;

        // render canvas to hi-res PNG
        const scale = 2;
        const tmp = document.createElement('canvas');
        tmp.width  = blk.el.width  * scale;
        tmp.height = blk.el.height * scale;
        const tctx = tmp.getContext('2d');
        tctx.scale(scale, scale);
        tctx.drawImage(blk.el, 0, 0);
        const imgData = tmp.toDataURL('image/png', 1.0);

        // Fit image to the page width and block height
        const imgW = innerW;
        const imgH = blockH - 8; // leave room under subtitle
        pdf.addImage(imgData, 'PNG', margin, y, imgW, imgH);

        y += blockH + gap;
      }

      pdf.save(`kfc-report-${(payload.monthTitle || '').replace(/\s+/g, '-')}.pdf`);
    });
  })();
</script>
</body>
</html>
