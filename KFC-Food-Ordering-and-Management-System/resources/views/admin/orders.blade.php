<!doctype html>
<!-- Author's Name: Lim Jing Min-->
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin • Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="/css/admin.css" />
  <style>
  
    .modal{position:fixed; inset:0; display:none; place-items:center; background:rgba(17,24,39,.5); z-index:50;}
    .modal.open{display:grid}
    .modal-card{width:min(820px,95vw); background:#fff; border:1px solid var(--gray-200); border-radius:18px; box-shadow:var(--shadow);}
    .modal-head{display:flex; justify-content:space-between; align-items:center; padding:14px 16px; border-bottom:1px solid var(--gray-200)}
    .modal-body{display:grid; gap:12px; padding:16px}
    .kv2{display:grid; grid-template-columns:1fr 1fr; gap:8px}
    .kv2 .row{display:flex; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px solid var(--gray-100)}
    .list-items{border:1px solid var(--gray-200); border-radius:12px; overflow:hidden}
    .list-items table{width:100%; border-collapse:collapse}
    .list-items th,.list-items td{padding:10px; border-top:1px solid var(--gray-200); font-size:14px; text-align:left}
    .modal-actions{display:flex; justify-content:flex-end; gap:10px; padding:12px 16px; border-top:1px solid var(--gray-200)}
    .btn[disabled],
    .btn[aria-disabled="true"],
    .btn.btn-disabled{
      background:#f3f4f6 !important;
      border-color:#e5e7eb !important;
      color:#9ca3af !important;
      cursor:not-allowed !important;
      box-shadow:none !important;
      filter:grayscale(100%);
    }
    .btn[disabled]:hover{ background:#f3f4f6 !important; }
    .muted-tiny{font-size:11px;color:#9ca3af;margin-top:4px}
    .icn-lock{margin-right:6px}
  </style>
</head>
<body>
  <div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar" aria-label="Admin navigation">
      <div class="brand">
        <div class="brand-logo-text">KFC <span class="brand-sub">Admin</span></div>
      </div>
      <nav class="side-nav">
          <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
          <a class="nav-link" href="{{ route('admin.menu') }}">Menu Items</a>
          <a class="nav-link active" href="{{ route('admin.orders') }}">Orders</a>
          <a class="nav-link" href="{{ route('admin.reviews') }}">Reviews</a>
          <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
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

    <!-- Main -->
    <main class="main">
      <!-- Topbar -->
      <header class="topbar">
        <button class="menu-toggle" aria-label="Toggle menu">☰</button>
        <div class="search">
  <form method="GET" action="{{ route('admin.orders') }}">
    <input
      id="q"
      name="q"
      type="search"
      class="search-input"
      value="{{ request('q') }}"
      placeholder="Search by Order ID, Customer, Phone…"
      autocomplete="off"
    />
    {{-- keep current status when searching --}}
    @if(request('status'))
      <input type="hidden" name="status" value="{{ request('status') }}">
    @endif

    <span class="search-icn" aria-hidden="true">🔎</span>
    <noscript><button class="btn btn-ghost xs" style="position:absolute;right:8px;top:8px">Go</button></noscript>
  </form>
</div>

      </header>

      <!-- Content -->
      <div class="container">

        <!-- Orders Table -->
        <article class="card col-span-2">
          <div class="card-head between">
            <h3>Orders</h3>

            {{-- Status Filter --}}
            <form method="GET" action="{{ route('admin.orders') }}" class="topbar-right" style="gap:10px">
              @foreach(request()->except('status') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
              @endforeach
              <label class="select-wrap">
                <select name="status" class="select" onchange="this.form.submit()">
                  <option value="">All Status</option>
                  <option value="{{ \App\Models\Order::RECEIVED }}"  @selected(request('status') === \App\Models\Order::RECEIVED)>Received</option>
                  <option value="{{ \App\Models\Order::PREPARING }}" @selected(request('status') === \App\Models\Order::PREPARING)>Preparing</option>
                  <option value="{{ \App\Models\Order::COMPLETED }}" @selected(request('status') === \App\Models\Order::COMPLETED)>Completed</option>
                </select>
              </label>
              <noscript><button class="btn btn-ghost">Filter</button></noscript>
            </form>
          </div>

          {{-- Flash messages --}}
          @if(session('success'))
            <div class="badge badge-emerald" style="display:inline-block; margin:8px 0;">{{ session('success') }}</div>
          @elseif(session('info'))
            <div class="badge badge-sky" style="display:inline-block; margin:8px 0;">{{ session('info') }}</div>
          @elseif(session('error'))
            <div class="badge badge-rose" style="display:inline-block; margin:8px 0;">{{ session('error') }}</div>
          @endif

          <div class="table-wrap">
            <table class="table" id="ordersTable">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Items</th>
                  <th class="right">Total (RM)</th>
                  <th>Status</th>
                  <th>Payment</th>
                  <th>Order Time</th>
                  <th class="right">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($orders as $order)
                 @php
                // Normalize payment status
                $payPretty = $order->payment->payment_status ?? 'Pending'; 
                $payKey    = strtolower($payPretty);                       

                // Badges
                $payBadge = match($payKey) {
                  'success'  => 'badge-emerald',
                  'failed'   => 'badge-rose',
                  'refunded' => 'badge-sky',
                  default    => 'badge-amber',
                };

                $statusBadge = match($order->status) {
                  \App\Models\Order::RECEIVED  => 'badge-amber',
                  \App\Models\Order::PREPARING => 'badge-sky',
                  \App\Models\Order::COMPLETED => 'badge-emerald',
                  default => 'badge'
                };

                // Only allow update if payment is Success and not already Completed
                $canUpdate = ($payKey === 'success' && $order->status !== \App\Models\Order::COMPLETED);

                // Optional: friendly reason (used in tooltip)
                $whyNot = null;
                if (!$canUpdate) {
                  $whyNot = match (true) {
                    $order->status === \App\Models\Order::COMPLETED
                      => 'Order already completed.',
                    $payKey === 'pending'
                      => 'Payment is Pending. Order has not yet been paid for.',
                    $payKey === 'failed'
                      => 'Payment Failed. The order payment was unsuccessful.',
                    default
                      => 'Payment not successful. You can only update when payment is Success.',
                  };
                }

                // Build items for modal
                $items = [];
                foreach ($order->orderDetails as $d) {
                  $items[] = [
                    $d->food->name ?? 'Item',
                    (int) $d->quantity,
                    (float) ($d->unit_price ?? 0),
                  ];
                }

                $dataOrder = [
                  'id'      => $order->id,
                  'name'    => $order->user->name   ?? 'N/A',
                  'phone'   => $order->user->phoneNo ?? '-',
                  'status'  => $order->status,
                  'payment' => [
                    'status' => $payPretty,
                    'method' => $order->payment->payment_method ?? '-',
                    'date'   => optional($order->payment?->payment_date)->format('d/m H:i')
                  ],
                  'time'  => optional($order->created_at)->format('H:i'),
                  'items' => $items,
                ];

                $countItems = (int) ($order->order_details_sum_quantity ?? 0);
              @endphp


                  <tr data-order='@json($dataOrder)'>
                    <td class="mono">#{{ $order->id }}</td>
                    <td>
                      {{ $order->user->name ?? 'N/A' }}
                      @if($order->user && $order->user->phoneNo)
                        <br><small class="muted">{{ $order->user->phoneNo }}</small>
                      @endif
                    </td>
                    <td>{{ $countItems }}</td>
                    <td class="right">{{ number_format($order->total_amount, 2) }}</td>
                    <td><span class="badge {{ $statusBadge }}">{{ $order->status }}</span></td>
                    <td><span class="badge {{ $payBadge }}">{{ $payPretty }}</span></td>
                    <td>{{ optional($order->created_at)->format('H:i') }}</td>
                    <td class="right">
                    <button type="button" class="btn btn-ghost xs js-details">Details</button>

                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" style="display:inline">
                      @csrf
                      {{-- preserve current filters/pagination --}}
                      @if(request('q'))      <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                      @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                      @if(request('page'))   <input type="hidden" name="page" value="{{ request('page') }}"> @endif

                      @if($canUpdate)
                        <button class="btn btn-ghost xs" title="Advance order status">Update</button>
                      @else
                        <button
                          class="btn btn-ghost xs btn-disabled"
                          disabled
                          aria-disabled="true"
                          title="{{ $whyNot ?? 'Payment not successful. You can only update when payment is Success.' }}"
                        >
                          <span class="icn-lock">🔒</span> Update
                        </button>
                        <div class="muted-tiny">{{ $whyNot ?? 'Requires payment: Success' }}</div>
                      @endif
                    </form>
                  </td>

                  </tr>
                @empty
                  <tr><td colspan="8" class="muted">No orders found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div style="margin-top:12px">{{ $orders->links() }}</div>
        </article>
      </div>
    </main>
  </div>

  <!-- Details Modal -->
  <div class="modal" id="orderModal" aria-hidden="true">
    <div class="modal-card">
      <div class="modal-head">
        <strong>Order Details</strong>
        <button class="btn btn-ghost xs" id="modalClose">✕</button>
      </div>
      <div class="modal-body">
        <div class="kv2">
          <div class="row"><span>Order ID</span><strong id="m_id">#—</strong></div>
          <div class="row"><span>Customer</span><strong id="m_cust">—</strong></div>
          <div class="row"><span>Phone</span><strong id="m_phone">—</strong></div>
          <div class="row"><span>Status</span><span class="badge" id="m_status">—</span></div>
          <div class="row"><span>Payment</span><strong id="m_payment">—</strong></div>
          <div class="row"><span>Order Time</span><strong id="m_time">—</strong></div>
        </div>
        <div class="list-items">
          <table>
            <thead><tr><th>Food</th><th>Qty</th><th>Price (RM)</th><th>Subtotal (RM)</th></tr></thead>
            <tbody id="m_items"></tbody>
            <tfoot><tr><th colspan="3" style="text-align:right;">Total</th><th id="m_total">0.00</th></tr></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    // mobile sidebar toggle
    const toggle=document.querySelector('.menu-toggle');
    const sidebar=document.querySelector('.sidebar');
    if(toggle&&sidebar){toggle.addEventListener('click',()=>sidebar.classList.toggle('open'));}

    // modal wiring
    const modal=document.getElementById('orderModal');
    const modalClose=document.getElementById('modalClose');
    const m_id=document.getElementById('m_id');
    const m_cust=document.getElementById('m_cust');
    const m_phone=document.getElementById('m_phone');
    const m_status=document.getElementById('m_status');
    const m_payment=document.getElementById('m_payment');
    const m_time=document.getElementById('m_time');
    const m_items=document.getElementById('m_items');
    const m_total=document.getElementById('m_total');

    document.querySelectorAll('.js-details').forEach(btn=>{
      btn.addEventListener('click',e=>{
        const tr=e.target.closest('tr');
        const data=JSON.parse(tr.getAttribute('data-order'));
        m_id.textContent='#'+data.id;
        m_cust.textContent=data.name;
        m_phone.textContent=data.phone;
        m_status.textContent=data.status;
        m_payment.textContent=data.payment.method+' • '+data.payment.status+(data.payment.date?' • '+data.payment.date:'');
        m_time.textContent=data.time;

        m_items.innerHTML='';
        let total=0;
        (data.items||[]).forEach(([name,qty,price])=>{
          const sub=qty*price;
          total+=sub;
          m_items.innerHTML+=`<tr><td>${name}</td><td>${qty}</td><td>${Number(price).toFixed(2)}</td><td>${sub.toFixed(2)}</td></tr>`;
        });
        m_total.textContent=total.toFixed(2);

        modal.classList.add('open');
      });
    });
    modalClose.addEventListener('click',()=>modal.classList.remove('open'));
    modal.addEventListener('click',e=>{if(e.target===modal)modal.classList.remove('open');});
  </script>
</body>
</html>
