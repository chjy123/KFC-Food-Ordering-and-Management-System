<?php
#author’s name： Yew Kai Quan
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoodResource;
use App\Models\Food;
use Illuminate\Http\Request;
use App\Models\Category;

class FoodApiController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category'); 
        $search   = $request->query('search');

        $foods = Food::query()
            ->with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($category !== null && $category !== '', function ($q) use ($category) {
                $needle = trim($category);

                $q->whereHas('category', function ($qq) use ($needle) {
                    $qq->where(function ($qq2) use ($needle) {
                        // Case-insensitive exact match on category_name
                        $qq2->whereRaw('LOWER(category_name) = ?', [mb_strtolower($needle)]);

                        // Also allow matching by numeric ID
                        if (is_numeric($needle)) {
                            $qq2->orWhere('id', (int) $needle);
                        }
                    });
                });
            })
            ->when($search !== null && $search !== '', function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower(trim($search)) . '%']);
            })
            ->orderBy('name')
            ->get();

        return FoodResource::collection($foods);
    }

    public function show($id)
    {
        $food = Food::with(['category', 'reviews.user'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($id);

        return new FoodResource($food);
    }

    public function categories()
{
    // fetch real columns, then map to the shape you want
    $rows = Category::query()
        ->select('id', 'category_name')
        ->orderBy('category_name')
        ->get();

    $data = $rows->map(fn($c) => [
        'id'   => $c->id,
        'name' => $c->category_name, // explicit mapping, accessor won't interfere
    ]);

    return response()->json($data->values());
}
}
