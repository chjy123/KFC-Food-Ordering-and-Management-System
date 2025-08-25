<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Food;
use App\Models\Review;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // get filters
        $q = $request->input('q');
        $categorySlug = $request->input('category');

        // get categories
        $categories = Category::all();

        // query foods
        $foods = Food::withCount('reviews')
                     ->withAvg('reviews', 'rating')
                     ->when($q, function ($query) use ($q) {
                         $query->where('name', 'like', "%$q%")
                               ->orWhere('description', 'like', "%$q%");
                     })
                     ->when($categorySlug, function ($query) use ($categorySlug) {
                         $query->whereHas('category', function ($q) use ($categorySlug) {
                             $q->where('slug', $categorySlug);
                         });
                     })
                     ->paginate(9);

        // eager load latest reviews
        $foodIds = $foods->pluck('id');
        $latestReviews = Review::whereIn('food_id', $foodIds)
                               ->orderBy('created_at', 'desc')
                               ->get()
                               ->groupBy('food_id');

        return view('menu', compact('categories', 'foods', 'latestReviews', 'q', 'categorySlug'));
    }
}
