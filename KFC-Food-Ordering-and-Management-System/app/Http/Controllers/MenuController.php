<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Food;
use App\Models\Review;
use App\Builders\FoodQueryBuilder;
use App\Builders\FoodQueryDirector;

class MenuController extends Controller
{
    // MENU LIST
     public function index(Request $request)
    {
        $category = $request->query('category');   // id or name
        $search   = $request->query('q', '');
        $sort     = $request->query('sort', 'name'); // optional: ?sort=price|reviews_count|reviews_avg_rating
        $dir      = $request->query('dir', 'asc');   // optional: ?dir=desc

        // Builder Pattern: construct the complex query step-by-step
        $foods = (new FoodQueryBuilder())
            ->withAvgRating()
            ->search($search)
            ->byCategory($category)
            ->sort($sort, $dir)
            ->paginate(12);

        $categories = Category::orderBy('category_name')->get();

        return view('user.menu', [
            'foods'       => $foods,
            'category'    => $category,
            'search'      => $search,
            'categories'  => $categories,
        ]);
    }

    public function show(Request $request, Food $food)
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

        $myReview = null;
        if ($request->user()) {
        $myReview = $food->reviews()
            ->where('user_id', $request->user()->id)
            ->first();
        }

        return view('user.food_show', [
            'food'        => $food,
            'reviews'     => $reviews,
            'avg'         => $avg,
            'reviewCount' => $reviewCount,
            'categories'  => $categories,
            'myReview'    => $myReview,
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
