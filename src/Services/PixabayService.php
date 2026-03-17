<?php

namespace hexa_package_pixabay\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use hexa_core\Models\Setting;

class PixabayService
{
    /**
     * @return string|null
     */
    private function getApiKey(): ?string
    {
        return Setting::getValue('pixabay_api_key');
    }

    /**
     * Test the API key.
     *
     * @param string|null $apiKey Override key to test.
     * @return array{success: bool, message: string}
     */
    public function testApiKey(?string $apiKey = null): array
    {
        $key = $apiKey ?? $this->getApiKey();
        if (!$key) {
            return ['success' => false, 'message' => 'No Pixabay API key configured.'];
        }

        try {
            $response = Http::timeout(10)
                ->get('https://pixabay.com/api/', ['key' => $key, 'q' => 'test', 'per_page' => 3]);

            if ($response->successful()) {
                $data = $response->json();
                return ['success' => true, 'message' => "Pixabay API key is valid. Total images available: " . number_format($data['totalHits'] ?? 0) . "."];
            }
            if ($response->status() === 401) {
                return ['success' => false, 'message' => 'Invalid API key.'];
            }
            return ['success' => false, 'message' => "Pixabay returned HTTP {$response->status()}."];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Search for photos.
     *
     * @param string $query
     * @param int $perPage
     * @param int $page
     * @return array{success: bool, message: string, data: array|null}
     */
    public function searchPhotos(string $query, int $perPage = 15, int $page = 1): array
    {
        $key = $this->getApiKey();
        if (!$key) {
            return ['success' => false, 'message' => 'No Pixabay API key configured.', 'data' => null];
        }

        try {
            $response = Http::timeout(15)
                ->get('https://pixabay.com/api/', [
                    'key' => $key,
                    'q' => $query,
                    'per_page' => min($perPage, 200),
                    'page' => $page,
                    'image_type' => 'photo',
                    'safesearch' => 'true',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $photos = collect($data['hits'] ?? [])->map(fn($p) => [
                    'source' => 'pixabay',
                    'id' => $p['id'],
                    'url_thumb' => $p['webformatURL'],
                    'url_full' => $p['largeImageURL'],
                    'url_large' => $p['largeImageURL'],
                    'alt' => $p['tags'] ?? '',
                    'photographer' => $p['user'] ?? '',
                    'photographer_url' => "https://pixabay.com/users/{$p['user']}-{$p['user_id']}/",
                    'width' => $p['imageWidth'],
                    'height' => $p['imageHeight'],
                    'pixabay_url' => $p['pageURL'],
                ])->toArray();

                return [
                    'success' => true,
                    'message' => count($photos) . ' photos found.',
                    'data' => ['photos' => $photos, 'total' => $data['totalHits'] ?? 0, 'page' => $page],
                ];
            }

            return ['success' => false, 'message' => "Pixabay returned HTTP {$response->status()}.", 'data' => null];
        } catch (\Exception $e) {
            Log::error('PixabayService::searchPhotos error', ['query' => $query, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'data' => null];
        }
    }
}
