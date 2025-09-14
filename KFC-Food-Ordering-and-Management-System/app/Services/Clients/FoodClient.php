<?php

namespace App\Services\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;

class FoodClient
{
    public function find(int $id): array
    {
        $key = "food:$id";
        return Cache::remember($key, 30, function () use ($id) {
            $resp = Http::acceptJson()
                ->timeout(5)
                ->get(config('services.menu.base_url') . "/api/v1/foods/{$id}");

            if ($resp->failed()) {
                $resp->throw();
            }
            return $resp->json();
        });
    }
}
