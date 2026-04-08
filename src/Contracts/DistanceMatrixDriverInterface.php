<?php

namespace Haroun\DistanceMatrix\Contracts;

interface DistanceMatrixDriverInterface
{
    /**
     * Calculate route for given coordinates.
     *
     * @param array $coordinates Array of ['lat' => float, 'lng' => float]
     * @return array Normalized response { distance: float, duration: float, geometry: string|array|null }
     */
    public function route(array $coordinates): array;

    /**
     * Calculate distance matrix for given coordinates.
     *
     * @param array $coordinates Array of ['lat' => float, 'lng' => float]
     * @return array Normalized response { distances: array, durations: array }
     */
    public function matrix(array $coordinates): array;
}
