@include('partials.header')
<link rel="stylesheet" href="{{ asset('css/menu.css') }}">

<div class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-12">

    <a href="{{ route('menu.index') }}" class="text-sm text-red-600 hover:underline">&larr; Back to Menu</a>

    <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Image / Gallery -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <img src="{{ $food->image_url ? asset('storage/'.$food->image_url) : 'https://via.placeholder.com/800x500?text=Food' }}"
             alt="{{ $food->name }}" class="w-full h-80 object-cover">
      </div>

      <!-- Summary -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $food->name }}</h1>
        <p class="mt-2 text-gray-600">{{ $food->description }}</p>

        <div class="mt-4 flex items-center gap-4">
          <span class="text-red-600 text-2xl font-extrabold">RM {{ number_format($food->price, 2) }}</span>
          {{-- SYNCED stats from controller --}}
          <div class="mt-2 flex items-center gap-2 text-gray-700">
            @php $roundedStars = (int) round((float)$avg); @endphp
            <div class="flex items-center">
              @for($i=1; $i<=5; $i++)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    class="w-5 h-5 {{ $i <= $roundedStars ? 'text-yellow-400' : 'text-gray-300' }} fill-current">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0l-2.802 2.036c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.88 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
              @endfor
            </div>
            <span  span class="font-semibold">{{ number_format((float)$avg, 1) }}</span>
            <span>·</span>
            <span>{{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review', $reviewCount) }}</span>
        </div>
      </div>
        

        @if($food->category)
          <p class="mt-2 text-sm text-gray-500">Category: <span class="font-semibold">{{ $food->category->name }}</span></p>
        @endif
      </div>
    </div>

    <!-- Reviews -->
    <section class="mt-10">
      <h2 class="text-2xl font-bold text-gray-900 mb-4">Reviews</h2>

      @auth
        <!-- Write/Update my review -->
        <form method="POST" action="{{ route('reviews.store', $food) }}" class="bg-white rounded-lg shadow p-4 mb-6">
          @csrf
          <div class="flex items-center gap-3">
            <label class="font-semibold">Your Rating:</label>
            <div class="stars">
              @for($i=1; $i<=5; $i++)
                <label>
                  <input type="radio" name="rating" value="{{ $i }}" class="sr-only"
                         {{ optional($myReview)->rating == $i ? 'checked' : '' }}>
                  <span class="star {{ optional($myReview)->rating >= $i ? 'on' : 'off' }}">★</span>
                </label>
              @endfor
            </div>
          </div>
          <textarea name="comment" class="w-full mt-3 rounded border px-3 py-2"
                    placeholder="Share your thoughts...">{{ old('comment', optional($myReview)->comment) }}</textarea>
          <div class="mt-3 flex items-center gap-3">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
              {{ $myReview ? 'Update Review' : 'Submit Review' }}
            </button>
          </div>
        </form>
           {{-- DELETE MY REVIEW (DELETE) --}}
            @if($myReview)
              <form method="POST" action="{{ route('reviews.destroy.mine', $food) }}" class="mb-6">
                @csrf
                @method('DELETE')
                <button class="text-red-600 hover:underline">Delete my review</button>
              </form>
            @endif
      @else
        <a href="{{ route('login') }}" class="text-red-600 hover:underline">Sign in to leave a review</a>
      @endauth

      <!-- List reviews -->
      <div class="grid gap-4">
        @forelse($reviews as $r)
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div class="font-semibold text-gray-800">{{ optional($r->user)->name ?? 'Anonymous' }}</div>
              <div class="stars">
                @for($i=1; $i<=5; $i++)
                  <span class="{{ $i <= (int)$r->rating ? 'star on' : 'star off' }}">★</span>
                @endfor
              </div>
            </div>
            <p class="mt-1 text-gray-700">{{ $r->comment }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $r->created_at?->diffForHumans() }}</p>
          </div>
        @empty
          <p class="text-gray-600">No reviews yet. Be the first to write one!</p>
        @endforelse
      </div>

      <div class="mt-6">
        {{ $reviews->links() }}
      </div>
    </section>
  </div>
</div>

@include('partials.footer')
