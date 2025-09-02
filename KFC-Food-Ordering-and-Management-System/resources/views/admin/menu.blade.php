<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Admin • Menu Items</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<head>
  <meta charset="utf-8" />
  <title>Admin • Menu Items</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
  
  <style>
    :root{
      --brand-700:#b91c1c; --brand-800:#991b1b; --brand-600:#dc2626;
    }

    .food-desc{min-height:36px}               
    .toolbar{display:flex;gap:10px;align-items:center}

    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .form-row{display:flex;flex-direction:column;gap:6px}
    .input,.textarea,.file,.select{
      width:100%;border:1px solid var(--gray-300);border-radius:12px;background:#fff;
      padding:10px 12px;font-size:14px;outline:none;
    }
    .textarea{min-height:96px;resize:vertical}

    .uploader{display:grid;grid-template-columns:160px 1fr;gap:14px;align-items:start}
    .thumb{
      width:160px;aspect-ratio:4/3;background:#f3f4f6;border:1px solid var(--gray-200);
      border-radius:12px;display:grid;place-items:center;color:#9ca3af;font-size:12px;
    }
  </style>
</head>
</head>
<body>
<div class="layout">
 <aside class="sidebar" aria-label="Admin navigation">
      <div class="brand">
  <div class="brand-logo-text">KFC <span class="brand-sub">Admin</span></div>
</div>

      <nav class="side-nav">
          <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
          <a class="nav-link active" href="{{ route('admin.menu') }}">Menu Items</a>
          <a class="nav-link" href="{{ route('admin.orders') }}">Orders</a>
          <a class="nav-link" href="{{ route('admin.reviews') }}">Reviews</a>
          <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
      </nav>

    </aside>


<main class="main">
    <header class="topbar">
      <div class="search">
  <form method="GET" action="{{ route('admin.menu') }}">
    <input
      id="q"
      type="search"
      name="q"
      class="search-input"
      value="{{ $q ?? '' }}"
      placeholder="Search foods by name…"
      autocomplete="off"
    />
    <span class="search-icn" aria-hidden="true">🔎</span>
    <noscript><button class="btn btn-ghost xs" style="position:absolute;right:8px;top:8px">Go</button></noscript>
  </form>
</div>
      <a class="btn btn-outline" href="#manageCategories"> Add Category</a>
   </header>
<div class="container">
<article class="card" style="margin-bottom:16px">
  <div class="card-head">
    <strong>Menu Items</strong>
    <div class="toolbar">
      <div style="display:flex; gap:8px; flex-wrap:wrap">
        @foreach($allCategories as $c)
          <a class="btn btn-ghost xs" href="#cat-{{ $c->id }}">{{ $c->category_name }}</a>
        @endforeach
      </div>
    </div>
  </div>

  <div class="modal" id="manageCategories" role="dialog" aria-modal="true" aria-label="Manage Categories">
  <div class="modal-card">
    <div class="modal-head">
      <strong>Manage Categories</strong>
      <a class="modal-close" href="#">✕</a>
    </div>

    <div class="modal-body">
      {{-- Create category --}}
      <form method="POST" action="{{ route('admin.categories.store') }}" style="margin-bottom:16px">
        @csrf
        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="form-row">
            <label>Name</label>
            <input class="input" name="category_name" required>
          </div>
          <div class="form-row">
            <label>Description (optional)</label>
            <input class="input" name="description">
          </div>
        </div>
        <div class="modal-actions" style="padding:0;border-top:none;justify-content:flex-start;margin-top:12px">
          <button class="btn btn-primary">Add Category</button>
        </div>
      </form>

      {{-- Existing categories (needs $allCategories) --}}
      <div class="card">
        <div class="card-head"><strong>Existing Categories</strong></div>
        <div class="card-body" style="display:grid;gap:10px">
          @foreach(($allCategories ?? $categories) as $c)
            <div style="display:grid;grid-template-columns:1fr auto;gap:10px;align-items:center">
              <div>
                <div style="font-weight:700">{{ $c->category_name ?? $c->name }}</div>
                <div style="font-size:12px;color:#6b7280">{{ $c->description }}</div>
              </div>
              <div style="display:flex;gap:8px">
                <a class="btn btn-ghost xs" href="#editCategory-{{ $c->id }}">Edit</a>
                <a class="btn btn-ghost xs" href="#deleteCategory-{{ $c->id }}">Delete</a>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

  @if ($errors->has('general'))
    <div class="card-body" style="color:#b91c1c">{{ $errors->first('general') }}</div>
  @elseif (session('status'))
    <div class="card-body" style="color:#065f46">{{ session('status') }}</div>
  @else
    <div class="card-body" style="font-size:12px; color:#4b5563">
      Tip: Click a pill to jump to a category. Use the buttons on each category to add foods inside it.
    </div>
  @endif
</article>



@forelse ($categories as $cat)
<section class="cat" id="cat-{{ $cat->id }}" style="margin-top:16px">
<div class="cat-head">
<div class="cat-title">{{ $cat->category_name }}</div>
<div class="cat-actions">
<a class="btn btn-ghost xs" href="#addFood-{{ $cat->id }}">Add Food</a>
<a class="btn btn-ghost xs" href="#editCategory-{{ $cat->id }}">Edit Category</a>
<a class="btn btn-ghost xs" href="#deleteCategory-{{ $cat->id }}">Delete</a>
</div>
</div>
<div class="card-body">
@if ($cat->foods->isEmpty())
<div style="color:#4b5563; font-size:14px">No foods found{{ $q ? ' for "'.$q.'"' : '' }}.</div>
@else
<div class="grid cols-3">
@foreach ($cat->foods as $food)
<article class="food-card">
<div class="food-media">
@php
$src = $food->image_url ? asset('storage/'.$food->image_url) : 'https://placehold.co/800x600?text=No+Image';
@endphp
<img src="{{ $src }}" alt="{{ $food->name }}" />
</div>
<div class="food-body">
<div class="food-row">
<div>
<div class="food-title">{{ $food->name }}</div>
<div class="food-desc">{{ $food->description }}</div>
</div>
<div class="price">RM {{ number_format($food->price,2) }}</div>
</div>
<div class="food-row">
<a class="btn btn-ghost xs" href="#editFood-{{ $food->id }}"><svg class="icn s"><use href="#i-pencil"/></svg> Edit</a>
<a class="btn btn-ghost xs" href="#deleteFood-{{ $food->id }}"><svg class="icn s"><use href="#i-trash"/></svg> Delete</a>
</div>
</div>
</article>
@endforeach
</div>
@endif
</div>
</section>
@empty
<div class="card"><div class="card-body">No categories yet. Add one to get started.</div></div>
@endforelse


</div>
</main>
</div>
{{-- ================== ICON SPRITE ================== --}}
<svg xmlns="http://www.w3.org/2000/svg" style="display:none">
  <symbol id="i-pencil" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94a2.12 2.12 0 0 0-3-3L3 17.25z"/><path d="M14.06 6.19l3.75 3.75"/></symbol>
  <symbol id="i-trash" viewBox="0 0 24 24"><path d="M4 7h16M7 7v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7M9 7V4h6v3"/><path d="M10 11v6M14 11v6"/></symbol>
</svg>

{{-- ================== Category Modals (edit/delete/add) ================== --}}
@foreach ($allCategories as $c)
  {{-- Edit Category --}}
  <div class="modal" id="editCategory-{{ $c->id }}" role="dialog" aria-modal="true" aria-label="Edit Category">
    <div class="modal-card">
      <div class="modal-head"><strong>Edit Category: {{ $c->category_name }}</strong><a class="modal-close" href="#">✕</a></div>
      <form class="modal-body" method="POST" action="{{ route('admin.categories.update',$c->id) }}">
        @csrf @method('PUT')
        <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:14px">
          <div class="form-row"><label>Name</label><input class="input" name="category_name" value="{{ $c->category_name }}" required></div>
          <div class="form-row"><label>Description</label><input class="input" name="description" value="{{ $c->description }}"></div>
        </div>
        <div class="modal-actions"><a class="btn btn-ghost" href="#">Cancel</a><button class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>

  {{-- Delete Category --}}
  <div class="modal" id="deleteCategory-{{ $c->id }}" role="dialog" aria-modal="true" aria-label="Delete Category">
    <div class="modal-card">
      <div class="modal-head"><strong style="color:#881337">Delete Category</strong><a class="modal-close" href="#">✕</a></div>
      <form class="modal-body" method="POST" action="{{ route('admin.categories.destroy',$c->id) }}">
        @csrf @method('DELETE')
        <p>Delete category <strong>{{ $c->category_name }}</strong>? Foods in this category may block deletion if constraints exist.</p>
        <div class="modal-actions"><a class="btn btn-ghost" href="#">Cancel</a><button class="btn btn-outline">Delete</button></div>
      </form>
    </div>
  </div>

  {{-- Add Food (preselected category) --}}
  <div class="modal" id="addFood-{{ $c->id }}" role="dialog" aria-modal="true" aria-label="Add Food">
    <div class="modal-card">
      <div class="modal-head"><strong>Add Food — {{ $c->category_name }}</strong><a class="modal-close" href="#">✕</a></div>
      <form class="modal-body" method="POST" action="{{ route('admin.foods.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns:160px 1fr; gap:14px; align-items:start" class="uploader">
          <div class="thumb" style="width:160px; aspect-ratio:4/3; background:#f3f4f6; border:1px solid var(--g200); border-radius:12px; display:grid; place-items:center; color:#9ca3af; font-size:12px">No image</div>
          <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:14px">
            <input type="hidden" name="category_id" value="{{ $c->id }}">
            <div class="form-row"><label>Food Name</label><input class="input" name="name" required></div>
            <div class="form-row"><label>Price (RM)</label><input class="input" type="number" step="0.01" min="0" name="price" required></div>
            <div class="form-row" style="grid-column:1/-1"><label>Description</label><textarea class="textarea" name="description"></textarea></div>
            <div class="form-row" style="grid-column:1/-1"><label>Image</label><input type="file" class="file" name="image" accept="image/*"></div>
          </div>
        </div>
        <div class="modal-actions"><a class="btn btn-ghost" href="#">Cancel</a><button class="btn btn-primary">Create Food</button></div>
      </form>
    </div>
  </div>
@endforeach


</body>
</html>