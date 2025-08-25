@include('partials.header')

<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-red-600 mb-6">Menu</h1>

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('menu.index') }}" class="flex flex-col md:flex-row gap-4 mb-8">
            <input type="search" name="q" placeholder="Search for food..."
                   value="{{ request('q') }}"
                   class="w-full md:flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            <select name="category"
                    class="w-full md:w-60 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                        {{ $cat->name }}
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
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <img src="{{ $food->image_url ?: 'https://via.placeholder.com/400x250?text=Food' }}"
                         alt="{{ $food->name }}" class="w-full h-48 object-cover rounded-t-lg">
                    <div class="p-4">
                        <h2 class="text-xl font-bold text-gray-800">{{ $food->name }}</h2>
                        <p class="text-gray-600 text-sm mb-2">{{ $food->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-red-600 font-semibold">RM {{ number_format($food->price, 2) }}</span>
                            <span>
                                @for($i=1; $i<=5; $i++)
                                    <span class="{{ $i <= round($food->rating_avg ?? 0) ? 'text-yellow-500' : 'text-gray-300' }}">★</span>
                                @endfor
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm">({{ number_format($food->rating_avg ?? 0,1) }}) · {{ $food->rating_count ?? 0 }} reviews</p>

                        <!-- Reviews -->
                        <details class="mt-4">
                            <summary class="cursor-pointer font-semibold text-gray-700">Latest Reviews</summary>
                            <div class="mt-2 space-y-3">
                                @php
                                    $reviews = $latestReviews[$food->id] ?? collect();
                                @endphp
                                @forelse($reviews as $r)
                                    <div class="border rounded p-2 bg-gray-50">
                                        <div class="flex justify-between text-sm text-gray-600">
                                            <span class="font-semibold">{{ $r->user_name }}</span>
                                            <span>
                                                @for($i=1; $i<=5; $i++)
                                                    <span class="{{ $i <= $r->rating ? 'text-yellow-500' : 'text-gray-300' }}">★</span>
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
    </div>
</div>

@include('partials.footer')
