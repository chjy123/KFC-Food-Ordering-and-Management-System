<?php
#author’s name： Yew Kai Quan
namespace App\Builders;

use App\Models\Food;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class FoodQueryBuilder
{
    /** @var Builder */
    protected Builder $query;

    /** Construct a fresh base query (acts like the “Product” under construction). */
    public function __construct()
    {
        // Base: relations + review metrics
        $this->query = Food::query()
            ->with('category')
            ->withCount('reviews'); // ->reviews_count
    }

    /** Optional: include review average rating (works on all Laravel versions). */
    public function withAvgRating(): self
    {
        // Prefer withAvg if available; otherwise fallback to subquery
        if (method_exists($this->query->getModel()->newQuery(), 'withAvg')) {
            $this->query->withAvg('reviews', 'rating'); // ->reviews_avg_rating
        } else {
            $this->query->addSelect([
                'reviews_avg_rating' => \App\Models\Review::selectRaw('AVG(rating)')
                    ->whereColumn('reviews.food_id', 'foods.id'),
            ]);
        }
        return $this;
    }

    /** Optional: filter by category (ID or name). */
    public function byCategory(null|int|string $category): self
    {
        if ($category === null || $category === '') {
            return $this;
        }

        if (is_numeric($category)) {
            $this->query->where('category_id', (int) $category);
            return $this;
        }

        // Resolve category by name (adjust column if yours differs)
        $cat = Category::where('name', $category)->first();
        if ($cat) {
            $this->query->where('category_id', $cat->id);
        }
        return $this;
    }

    /** Optional: search by food name (LIKE). */
    public function search(?string $q): self
    {
        $q = trim((string) $q);
        if ($q !== '') {
            $this->query->where('name', 'like', "%{$q}%");
        }
        return $this;
    }

    /** Optional: sort field & direction (safe allow-list). */
    public function sort(string $field = 'name', string $dir = 'asc'): self
    {
        $allowed = ['name', 'price', 'reviews_count', 'reviews_avg_rating', 'created_at'];
        $field = in_array($field, $allowed, true) ? $field : 'name';
        $dir   = strtolower($dir) === 'desc' ? 'desc' : 'asc';

        // If sorting by computed avg, ensure it's present
        if ($field === 'reviews_avg_rating') {
            $this->withAvgRating();
        }

        $this->query->orderBy($field, $dir);
        return $this;
    }

    /** Finish: get collection (no pagination). */
    public function get()
    {
        return $this->query->get();
    }

    /** Finish: paginate with query string kept (for filters in URL). */
    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return $this->query->paginate($perPage)->withQueryString();
    }

    /** Advanced escape hatch if you ever need the underlying Builder. */
    public function toBase(): Builder
    {
        return $this->query;
    }
}
