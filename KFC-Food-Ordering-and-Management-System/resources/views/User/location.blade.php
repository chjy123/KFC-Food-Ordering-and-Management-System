{{-- resources/views/location.blade.php --}}
@include('partials.header')

<link rel="stylesheet" href="{{ asset('css/locations.css') }}"/>

<main class="site-container">
  <h1 class="page-title">Locations</h1>
  @php
  $back = isset($next) && $next ? $next : route('locations.index');
  @endphp
  <div class="back-row" style="margin-bottom:1rem">
    <a class="btn link" href="{{ $back }}">← Back</a>
  </div>
  <p class="sub">Click a card to preview on the map, or search by city/state/mall.</p>

  <div class="toolbar">
    <input id="locSearch" class="search" type="search"
           placeholder="Search by city, state, mall, or address…">
    <button id="locClearBtn" class="btn secondary" type="button">Clear</button>
  </div>

  <iframe id="locMap" class="map"
          src="https://www.google.com/maps?q=KFC%20Malaysia&output=embed&z=6"
          loading="lazy"></iframe>

  <div id="locGrid" class="grid">
    @foreach($locations as $loc)
      @php
        $name  = $loc['name']   ?? '';
        $addr  = $loc['address']?? '';
        $city  = $loc['city']   ?? '';
        $state = $loc['state']  ?? '';
        $hours = $loc['hours']  ?? '';
        $searchBlob = strtolower(trim($name.' '.$addr.' '.$city.' '.$state));
        $embed = "https://www.google.com/maps?q=".urlencode($name.' '.$addr)."&output=embed&z=16";
        $link  = "https://www.google.com/maps/search/?api=1&query=".urlencode($name.' '.$addr);
      @endphp

      <article class="card"
               data-search="{{ $searchBlob }}"
               data-embed="{{ $embed }}"
               tabindex="0">
        <h3 class="title">{{ $name }}</h3>
        <div class="meta">
          @if($city)<span class="pill">{{ $city }}</span>@endif
          @if($state)<span class="pill">{{ $state }}</span>@endif
        </div>
        @if($addr)<p class="addr">{{ $addr }}</p>@endif
        <div class="row">
          <span class="meta">{{ $hours ? "Hours: $hours" : '' }}</span>
          <a class="btn" target="_blank" href="{{ $link }}">Open in Maps</a>
        </div>
      </article>
    @endforeach
  </div>

  <footer class="tip">Tip: type “Johor Bahru” or “Penang” in the search box to filter.</footer>
</main>

@include('partials.footer')

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const mapEl    = document.getElementById('locMap');
    const searchEl = document.getElementById('locSearch');
    const clearBtn = document.getElementById('locClearBtn');
    const gridEl   = document.getElementById('locGrid');

    const getCards = () => Array.from(gridEl.querySelectorAll('.card'));
    const norm = s => (s || '').toLowerCase().trim();

    function previewOnMap(card){
      const src = card.getAttribute('data-embed');
      if (src) mapEl.src = src;
    }

    function applyFilter(){
      const q = norm(searchEl.value);
      let firstVisible = null;

      getCards().forEach(card => {
        const blob = norm(card.getAttribute('data-search'));
        const match = !q || blob.includes(q);
        card.classList.toggle('hide', !match);
        if (match && !firstVisible) firstVisible = card;
      });

      if (firstVisible) previewOnMap(firstVisible);
    }

    // events
    searchEl.addEventListener('input', applyFilter);
    clearBtn.addEventListener('click', () => { searchEl.value = ''; applyFilter(); searchEl.focus(); });

    // card clicks preview map
    getCards().forEach(c => c.addEventListener('click', () => previewOnMap(c)));

    // initial render
    applyFilter();
  });
</script>
