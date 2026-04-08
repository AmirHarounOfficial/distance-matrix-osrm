# Distance Matrix for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/haroun/distance-matrix.svg)](https://packagist.org/packages/haroun/distance-matrix)
[![License](https://img.shields.io/packagist/l/haroun/distance-matrix.svg)](https://packagist.org/packages/haroun/distance-matrix)

A professional Laravel package that integrates with OSRM (Open Source Routing Machine) to provide routing and distance matrix functionality.

## Requirements

- PHP 8.0+
- Laravel 9.x, 10.x, 11.x, or 12.x

## Installation

You can install the package via Composer:

```bash
composer require haroun/distance-matrix
```

The package uses Laravel's auto-discovery, so the service provider and facade are registered automatically.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="distance-matrix-config"
```

In your `.env` file, you can optionally configure the OSRM Base URL:

```env
OSRM_BASE_URL=http://router.project-osrm.org
DISTANCE_MATRIX_DRIVER=osrm
```

### Configuration Options

| Option | Default | Description |
|---|---|---|
| `driver` | `osrm` | The routing driver to use (future: `google`, `mapbox`) |
| `osrm.base_url` | `http://router.project-osrm.org` | OSRM server base URL |
| `osrm.profile` | `driving` | Routing profile (`driving`, `car`, `bike`, `foot`) |
| `osrm.timeout` | `10` | HTTP request timeout in seconds |
| `osrm.retry.enabled` | `true` | Enable automatic request retries |
| `osrm.retry.times` | `3` | Number of retry attempts |
| `osrm.retry.sleepMs` | `1000` | Milliseconds between retries |
| `osrm.cache.enabled` | `false` | Enable response caching |
| `osrm.cache.ttl` | `3600` | Cache TTL in seconds |

## Usage

```php
use Haroun\DistanceMatrix\DistanceMatrix;

// 1. Calculate Route
$route = DistanceMatrix::route([
    ['lat' => 52.53214, 'lng' => 13.39957], // Start point
    ['lat' => 52.54012, 'lng' => 13.39867], // End point
]);

// Returns:
// [
//    'distance' => 1245.5,   // meters
//    'duration' => 140.2,    // seconds
//    'geometry' => '...'     // encoded polyline
// ]

// 2. Calculate Distance Matrix
$matrix = DistanceMatrix::matrix([
    ['lat' => 52.53214, 'lng' => 13.39957],
    ['lat' => 52.54012, 'lng' => 13.39867],
    ['lat' => 52.54512, 'lng' => 13.40112],
]);

// Returns:
// [
//    'distances' => [
//        [0, 1245.5, 2300.1],
//        ...
//    ],
//    'durations' => [
//        [0, 140.2, 250.5],
//        ...
//    ]
// ]
```

## Extending with Custom Drivers

You can implement the `DistanceMatrixDriverInterface` to add your own routing provider:

```php
use Haroun\DistanceMatrix\Contracts\DistanceMatrixDriverInterface;

class GoogleMapsService implements DistanceMatrixDriverInterface
{
    public function route(array $coordinates): array
    {
        // Your implementation
    }

    public function matrix(array $coordinates): array
    {
        // Your implementation
    }
}
```

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
