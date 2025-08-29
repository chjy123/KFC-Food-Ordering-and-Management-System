<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Food;
use App\Models\Review;

class MenuController extends Controller
{
    // MENU LIST
     public function index(Request $request)
    {
        $category = $request->query('category'); // slug or name
        $search   = $request->query('q', '');

        // Detect if withAvg is available (Laravel 9+)
        $builderSupportsWithAvg = method_exists((new Food)->newQuery(), 'withAvg');

        $foods = Food::query()
            ->with('category')
            ->with('reviews')
            ->withCount('reviews') // ->reviews_count
            ->when($builderSupportsWithAvg, function ($q) {
                $q->withAvg('reviews', 'rating'); // ->reviews_avg_rating
            }, function ($q) {
                // Fallback for Laravel < 9
                $q->addSelect([
                    'reviews_avg_rating' => Review::selectRaw('AVG(rating)')
                        ->whereColumn('reviews.food_id', 'foods.id'),
                ]);
            })
            ->when($category, function ($q) use ($category) {
                $q->whereHas('category', function ($qq) use ($category) {
                    if (is_numeric($category)) {
                        $qq->where('id', $category);        // filter by ID
                    } else {
                        $qq->where('name', $category);      // filter by name
                    }
                });
            })
            ->when(strlen($search) > 0, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();
        
        // 1. Start with a query builder
        $foodsQuery = Food::query()
            ->with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // 2. Apply search filter
        if ($search !== '') {
            $foodsQuery->where('name', 'like', "%{$search}%");
        }

        // 3. Apply category filter
        if (!is_null($category) && $category !== '') {
            if (is_numeric($category)) {
                // filter directly by id
                $foodsQuery->where('category_id', (int)$category);
            } else {
                // resolve by name (change 'name' to your actual column if different)
                $cat = Category::where('name', $category)->first();
                if ($cat) {
                    $foodsQuery->where('category_id', $cat->id);
                }
            }
        }

        $foods = $foodsQuery->orderBy('name')->paginate(12)->withQueryString();    
            
        $categories = Category::orderBy('category_name')->get();

        return view('user.menu', [
            'foods'    => $foods,
            'category' => $category,
            'search'   => $search,
            'categories' => $categories,
        ]);
    }

    public function show(Food $food)
    {
        // Ensure category is available for the header
        $food->load('category');
        $food->loadAvg('reviews', 'rating')
             ->loadCount('reviews');

        // Paginated reviews for the list below
        $reviews = $food->reviews()
            ->with('user:id,name') // load reviewer name if available
            ->latest()
            ->paginate(5);

        // Consistent stats for the header
        $food->loadCount('reviews'); // ->reviews_count
        $reviewCount = $food->reviews_count;

        // Average (Laravel 9+ or fallback)
        if (method_exists($food, 'loadAvg')) {
            $food->loadAvg('reviews', 'rating'); // ->reviews_avg_rating
            $avg = round($food->reviews_avg_rating ?? 0, 1);
        } else {
            $avg = round((float) $food->reviews()->avg('rating'), 1);
        }

        $categories = Category::orderBy('category_name')->get();

        return view('user.food_show', [
            'food'        => $food,
            'reviews'     => $reviews,
            'avg'         => $avg,
            'reviewCount' => $reviewCount,
            'categories'  => $categories,
        ]);
    }

    // === review handlers (one per user per food) ===
    public function storeOrUpdateMyReview(Request $request, Food $food)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review = Review::firstOrNew([
            'food_id' => $food->id,
            'user_id' => $request->user()->id,
        ]);
        $review->fill($data)->save();

        return back()->with('status', $review->wasRecentlyCreated ? 'Review submitted.' : 'Review updated.');
    }

    public function destroyMyReview(Request $request, Food $food)
    {
        $review = Review::where('food_id', $food->id)->where('user_id', $request->user()->id)->first();
        if ($review) {
            $review->delete();
            return back()->with('status', 'Your review was deleted.');
        }
        return back()->with('status', 'No review to delete.');
    }
}
