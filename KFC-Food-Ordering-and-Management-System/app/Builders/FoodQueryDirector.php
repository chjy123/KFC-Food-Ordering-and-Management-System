<?php
#author’s name： Yew Kai Quan
namespace App\Builders;

class FoodQueryDirector
{
    public function buildList(
        FoodQueryBuilder $builder,
        ?string $search = null,
        $category = null,
        string $sortField = 'name',
        string $sortDir = 'asc',
        int $perPage = 12
    ) {
        return $builder
            ->withAvgRating()
            ->search($search)
            ->byCategory($category)
            ->sort($sortField, $sortDir)
            ->paginate($perPage);
    }
}
