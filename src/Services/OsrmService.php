<?php

namespace Haroun\DistanceMatrix\Services;

use Haroun\DistanceMatrix\Contracts\DistanceMatrixDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class OsrmService implements DistanceMatrixDriverInterface
{
    protected array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function formatCoordinates(array $coordinates): string
    {
        // OSRM expects lng,lat
        return collect($coordinates)
            ->map(fn($coord) => "{$coord['lng']},{$coord['lat']}")
            ->implode(';');
    }

    public function route(array $coordinates): array
    {
        if (count($coordinates) < 2) {
            throw new Exception("Route requires at least 2 coordinates.");
        }

        $coordsString = $this->formatCoordinates($coordinates);
        $profile = $this->config['profile'] ?? 'driving';
        
        $url = "{$this->config['base_url']}/route/v1/{$profile}/{$coordsString}";

        $response = $this->makeRequest($url, [
            'overview' => 'false'
        ]);

        if (empty($response['routes'])) {
            throw new Exception("OSRM Route Error: Cannot calculate route.");
        }

        $route = $response['routes'][0];

        return [
            'distance' => (float) ($route['distance'] ?? 0),
            'duration' => (float) ($route['duration'] ?? 0),
            'geometry' => $route['geometry'] ?? null,
        ];
    }

    public function matrix(array $coordinates): array
    {
        if (count($coordinates) < 2) {
            throw new Exception("Matrix requires at least 2 coordinates.");
        }

        $coordsString = $this->formatCoordinates($coordinates);
        $profile = $this->config['profile'] ?? 'driving';

        $url = "{$this->config['base_url']}/table/v1/{$profile}/{$coordsString}";

        $response = $this->makeRequest($url, [
            'annotations' => 'duration,distance'
        ]);

        return [
            'distances' => $response['distances'] ?? [],
            'durations' => $response['durations'] ?? [],
        ];
    }

    protected function makeRequest(string $url, array $query = []): array
    {
        $cacheEnabled = $this->config['cache']['enabled'] ?? false;
        $cacheTtl = $this->config['cache']['ttl'] ?? 3600;
        
        $cacheKey = 'osrm_distance_matrix_' . md5($url . serialize($query));

        if ($cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $retryTimes = $this->config['retry']['times'] ?? 3;
        $retrySleep = $this->config['retry']['sleepMs'] ?? 1000;
        $timeout = $this->config['timeout'] ?? 10;

        $request = Http::timeout($timeout);

        if ($this->config['retry']['enabled'] ?? true) {
            $request = $request->retry($retryTimes, $retrySleep);
        }

        $response = $request->get($url, $query);

        if ($response->failed()) {
            throw new Exception("OSRM API Request Failed: " . $response->body());
        }

        $data = $response->json();

        if (isset($data['code']) && $data['code'] !== 'Ok') {
            throw new Exception("OSRM API Error: " . ($data['message'] ?? $data['code']));
        }

        if ($cacheEnabled) {
            Cache::put($cacheKey, $data, $cacheTtl);
        }

        return $data;
    }
}
