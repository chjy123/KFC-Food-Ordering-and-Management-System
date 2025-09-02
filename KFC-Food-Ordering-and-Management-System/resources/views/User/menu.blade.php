@include('partials.header')

<!-- Link external CSS -->
<link rel="stylesheet" href="{{ asset('css/menu.css') }}">

<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-red-600 mb-6">Menu</h1>

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('menu.index') }}" class="flex flex-col md:flex-row gap-4 mb-8">
            <input type="search" name="q" placeholder="Search for food..."
                   value="{{ request('q') }}"
                   class="w-full md:flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            <select id="categorySelect" name="category"
            class="border rounded px-3 py-2 w-full md:w-60">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string)request('category')===(string)$cat->id ? 'selected' : '' }}>
                {{ $cat->category_name }}
                </option>
            @endforeach
            </select>
            <button type="submit"
                    class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                Apply
            </button>
        </form>
        
        <!-- Food Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($foods as $food)
            @php
            // Both Laravel 9+ (withAvg) and fallback case supported:
            $avg   = isset($food->reviews_avg_rating) && $food->reviews_avg_rating !== null
                    ? number_format((float)$food->reviews_avg_rating, 1)
                    : number_format(0, 1);
            $count = (int)($food->reviews_count ?? 0);

            // Simple integer for drawing stars if you want
            $roundedStars = (int) round((float) $avg);
            @endphp
                <div class="menu-card bg-white rounded-lg shadow hover:shadow-lg transition">
                    {{-- clickable image --}}
                    <a href="{{ route('menu.show', $food) }}">
                    <img src="{{ $food->image_url ? asset('storage/'.$food->image_url) : 'https://via.placeholder.com/800x500?text=Food' }}"
                    alt="{{ $food->name }}" class="w-full h-48 object-cover rounded-t-lg">
                    </a>
                <div class="p-4">
                    {{-- clickable title --}}
                    <a href="{{ route('menu.show', $food) }}" class="text-xl font-bold text-gray-800 hover:text-red-600">
                        {{ $food->name }}
                    </a>
                    <p class="text-gray-600 text-sm mb-2">{{ $food->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-red-600 font-semibold">RM {{ number_format($food->price, 2) }}</span>
                            {{-- Category --}}
                            @if($food->category)
                            <div class="text-sm text-gray-600 mt-1">
                                Category: <span class="font-semibold">{{ $food->category->name }}</span>
                            </div>
                            @endif
                             {{-- Rating & Count (SYNCED) --}}
                            <div class="mt-2 flex items-center gap-2 text-sm text-gray-700">
                            <div class="flex items-center">
                                {{-- optional: draw 5 stars based on $roundedStars --}}
                                @for($i=1; $i<=5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    class="w-4 h-4 {{ $i <= $roundedStars ? 'text-yellow-400' : 'text-gray-300' }} fill-current">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0l-2.802 2.036c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.88 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                            <span class="font-semibold">{{ $avg }}</span>
                            <span>·</span>
                            <span>{{ $count }} {{ \Illuminate\Support\Str::plural('review', $count) }}</span>
                            </div>
                        </div>
                        </a>
                        <!-- Reviews -->
                        <details class="mt-4 review">
                            <summary class="cursor-pointer font-semibold text-gray-700">Latest Reviews</summary>
                            <div class="mt-2 space-y-3">
                               @php
                                    // Prefer the injected array; if missing, fall back to the eager-loaded relation
                                    $reviews = $latestReviews[$food->id] ?? $food->reviews->take(3);
                                @endphp
                                @forelse($reviews as $r)
                                    <div class="review-item border rounded p-2 bg-gray-50">
                                        <div class="flex justify-between text-sm text-gray-600">
                                            <span class="font-semibold">{{ $r->name ?? ($r->user_name ?? optional($r->user)->name ?? 'Anonymous') }}</span>
                                            <span class="stars">
                                                @for($i=1; $i<=5; $i++)
                                                    <span class="{{ $i <= $r->rating ? 'star on' : 'star off' }}">★</span>
                                                @endfor
                                            </span>
                                        </div>
                                        <p class="text-gray-700">{{ $r->comment }}</p>
                                        <p class="text-xs text-gray-400">{{ $r->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">No reviews yet.</p>
                                @endforelse
                            </div>
                        </details>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-600">No food items found.</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $foods->links() }}
        </div>

        <script>
        document.getElementById('categorySelect').addEventListener('change', function() {
            this.form.submit();
        });
</script>
    </div>
</div>

@include('partials.footer')
