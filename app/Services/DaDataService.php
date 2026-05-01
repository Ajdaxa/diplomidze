<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DaDataService
{
    public function suggestAddress(string $query): array
    {
        if (! $query) {
            return [];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Token '.config('services.dadata.api_key'),
            'X-Secret' => config('services.dadata.secret_key'),
        ])->post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
            'query' => $query,
            'count' => 5,
        ]);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('suggestions', []))
            ->map(fn ($item) => [
                'value' => $item['value'] ?? '',
                'city' => $item['data']['city'] ?? null,
                'street' => $item['data']['street_with_type'] ?? null,
                'house' => $item['data']['house'] ?? null,
            ])->all();
    }
}
