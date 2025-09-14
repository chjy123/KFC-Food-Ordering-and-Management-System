<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MenuApiClient
{
    public function listFoods(?string $q = null): array
    {
        $params = [];
        if ($q !== null && $q !== '') {
            $params['q'] = $q;
        }

        $resp = Http::baseUrl(config('services.menu.base'))
            ->acceptJson()
            ->timeout(5)
            ->retry(2, 200)
            ->get('/api/v1/foods', $params);

        if (! $resp->ok()) {
            Log::warning('Menu API error', ['status' => $resp->status(), 'body' => $resp->body()]);
            return ['data' => [], 'meta' => ['page' => 1, 'per_page' => 0, 'total' => 0]];
        }

        return [
            'data' => $resp->json('data', []),
            'meta' => $resp->json('meta', ['page' => 1, 'per_page' => 0, 'total' => 0]),
        ];
    }
}

