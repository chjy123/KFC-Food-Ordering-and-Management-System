<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <link rel="stylesheet" href="/css/admin.css" />

  <style>
  /* Header summary */
  .rev-summary{display:grid; grid-template-columns:220px 1fr; gap:16px; margin-bottom:14px}
  .rev-avg{border:1px solid var(--gray-200); border-radius:14px; padding:14px; background:#fff; text-align:center}
  .rev-avg .num{font-size:34px; font-weight:800}
  .rev-avg .stars{color:#f59e0b; letter-spacing:1px; margin-top:2px}
  .rev-avg .muted{color:var(--gray-600); font-size:12px}

  .rev-dist{display:grid; gap:8px}
  .rev-row{display:grid; grid-template-columns:46px 1fr 38px; gap:8px; align-items:center;
           padding:8px 10px; border:1px solid var(--gray-200); border-radius:12px; background:#fff; text-decoration:none; color:inherit}
  .rev-row:hover{background:#fafafa}
  .rev-row .bar{height:10px; background:var(--gray-100); border-radius:999px; overflow:hidden}
  .rev-row .bar i{display:block; height:100%; background:linear-gradient(#f87171,#dc2626)}

  /* Review cards */
  .review-list{display:grid; gap:12px}
  .review-card{display:grid; grid-template-columns:48px 1fr auto; gap:12px; align-items:start;
               padding:12px 14px; border:1px solid var(--gray-200); border-radius:14px; background:#fff}
  .review-card:hover{box-shadow:var(--shadow)}
  .avatar{width:48px; height:48px; border-radius:50%; background:var(--red-700); color:#fff;
          display:grid; place-items:center; font-weight:800}
  .pill{display:inline-block; padding:2px 8px; border:1px solid var(--gray-200); background:var(--gray-100);
        border-radius:999px; font-size:12px; font-weight:600; margin-right:6px}
  .stars{color:#f59e0b; letter-spacing:1px}
  .meta .row1{font-weight:700}
  .meta .row1 .muted{margin-left:6px}
  .meta .row2{margin-top:2px; display:flex; align-items:center; gap:6px}
  .comment{margin:.4rem 0 0; color:var(--gray-900);
           display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden}
  .time{color:var(--gray-600); font-size:12px; white-space:nowrap}
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
          <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
          <a class="nav-link" href="{{ route('admin.menu') }}">Menu Items</a>
          <a class="nav-link" href="{{ route('admin.orders') }}">Orders</a>
          <a class="nav-link active" href="{{ route('admin.reviews') }}">Reviews</a>
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
  <header class="topbar">
    <button class="menu-toggle" aria-label="Toggle menu">☰</button>

    <div class="search">
      <form method="GET" action="{{ route('admin.reviews') }}">
        <input id="q" name="q" type="search" class="search-input"
               value="{{ $q ?? '' }}" placeholder="Search by customer, food, or comment…" autocomplete="off"/>
        <span class="search-icn" aria-hidden="true">🔎</span>
      </form>
    </div>

    <form method="GET" action="{{ route('admin.reviews') }}" style="margin-left:auto;display:flex;gap:8px">
      <input type="hidden" name="q" value="{{ $q }}">
      <select name="rating" class="select" onchange="this.form.submit()">
        <option value="">All Ratings</option>
        @for($i=5;$i>=1;$i--)
          <option value="{{ $i }}" @selected($rating===$i)>{{ $i }}★</option>
        @endfor
      </select>
      <noscript><button class="btn btn-ghost xs">Filter</button></noscript>
    </form>
  </header>

  <div class="container">
    <article class="card">
  <div class="card-head">
    <strong>Customer Reviews</strong>
  </div>
  <div class="card-body">

    {{-- Summary header --}}
    <div class="rev-summary">
      <div class="rev-avg">
        <div class="num">{{ number_format($avg,1) }}</div>
        <div class="stars" aria-label="{{ $avg }} out of 5">
          {!! str_repeat('★', round($avg)) . str_repeat('☆', 5-round($avg)) !!}
        </div>
        <div class="muted">{{ $total }} total reviews</div>
      </div>

      <div class="rev-dist">
  @for ($i = 5; $i >= 1; $i--)
    @php
      $count = $counts[$i] ?? 0;
      $pct   = $total ? round($count / $total * 100) : 0;
      $params = array_filter(['q' => $q, 'rating' => $i], fn($v) => $v !== null && $v !== '');
    @endphp

    <a class="rev-row" href="{{ route('admin.reviews', $params) }}">
      <span>{{ $i }}★</span>
      <span class="bar">
        <i style="width: <?php echo $pct; ?>%;"></i>
      </span>
      <span class="mono">{{ $count }}</span>
    </a>
  @endfor
</div>

      </div>
    </div>

    {{-- Review cards --}}
    <div class="review-list">
      @forelse ($reviews as $r)
        @php
          $initial = strtoupper(mb_substr($r->user->name ?? 'U', 0, 1));
          $when    = $r->review_date
                        ? \Illuminate\Support\Carbon::parse($r->review_date)->format('d/m H:i')
                        : optional($r->created_at)->format('d/m H:i');
        @endphp

        <article class="review-card">
          <div class="avatar">{{ $initial }}</div>

          <div class="meta">
            <div class="row1">
              {{ $r->user->name ?? '—' }}
              @if($r->user?->phoneNo)<span class="muted">{{ $r->user->phoneNo }}</span>@endif
            </div>
            <div class="row2">
              <span class="pill">{{ $r->food->name ?? '—' }}</span>
              <span class="stars" aria-label="{{ $r->rating }} out of 5">
                {!! str_repeat('★', (int)$r->rating) . str_repeat('☆', 5 - (int)$r->rating) !!}
              </span>
              <small class="muted">({{ $r->rating }})</small>
            </div>
            <p class="comment">{{ $r->comment }}</p>
          </div>

          <div class="time">{{ $when }}</div>
        </article>
      @empty
        <div class="muted">No reviews found.</div>
      @endforelse
    </div>

    {{-- Pagination --}}
    <div style="margin-top:12px">{{ $reviews->links() }}</div>
  </div>
</article>

  </div>
</main>

{{-- Small helpers for styling (optional) --}}
<style>
  .table { width:100%; border-collapse:separate; border-spacing:0; }
  .table th, .table td { padding:12px 10px; border-bottom:1px solid var(--gray-200); vertical-align:top; }
  .mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
  .muted { color: var(--gray-500); font-size: 12px; }
</style>


</body>
</html>
