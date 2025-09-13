<?php
#author’s name： Yew Kai Quan
namespace App\Services;

use App\Http\Models\Food;
use App\Http\Models\Review;

class FoodService
{
    public function listFoods(array $filters = [], int $perPage = 12)
    {
        $supportsWithAvg = method_exists(\Illuminate\Database\Eloquent\Builder::class, 'withAvg');

        $q = Food::query()
            ->with(['category:id,name,slug'])
            ->withCount('reviews')
            ->when($supportsWithAvg, fn($qq) => $qq->withAvg('reviews', 'rating'),
                fn($qq) => $qq->addSelect([
                    'reviews_avg_rating' => Review::selectRaw('COALESCE(AVG(rating),0)')
                        ->whereColumn('reviews.food_id', 'foods.id'),
                ])
            )
            ->when(!empty($filters['category']), function ($qq) use ($filters) {
                $c = $filters['category'];
                $qq->whereHas('category', fn($q2) => $q2->where('slug',$c)->orWhere('name',$c));
            })
            ->when(!empty($filters['search']), function ($qq) use ($filters) {
                $s = $filters['search'];
                $qq->where(fn($q2) => $q2->where('name','like',"%{$s}%")->orWhere('description','like',"%{$s}%"));
            })
            ->when(isset($filters['min_rating']), fn($qq) => $qq->having('reviews_avg_rating', '>=', $filters['min_rating']));

        $sort = $filters['sort'] ?? 'name';
        $dir  = $filters['dir']  ?? 'asc';
        $col  = match ($sort) {
            'rating'  => 'reviews_avg_rating',
            'reviews' => 'reviews_count',
            default   => $sort,
        };

        return $q->orderBy($col, $dir)->paginate($perPage)->appends($filters);
    }

    public function getFoodWithStats($food)
    {
        $food->loadMissing(['category:id,name,slug'])->loadCount('reviews');

        if (method_exists(\Illuminate\Database\Eloquent\Builder::class, 'withAvg')) {
            $food->loadAvg('reviews', 'rating');
        } else {
            $food->setAttribute('reviews_avg_rating', (float) $food->reviews()->avg('rating') ?? 0);
        }

        return $food;
    }
}
