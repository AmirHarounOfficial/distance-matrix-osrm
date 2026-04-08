# Distance Matrix for Laravel

A professional Laravel package that integrates with OSRM (Open Source Routing Machine) to provide routing and distance matrix functionality.

## Installation

Since this is a local package, you can install it by adding the local repository to your `composer.json`:

```json
    "repositories": [
        {
            "type": "path",
            "url": "packages/haroun/distance-matrix"
        }
    ],
```

Then run:

```bash
composer require haroun/distance-matrix
```

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
//    'distance' => 1245.5,
//    'duration' => 140.2,
//    'geometry' => '...' 
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
