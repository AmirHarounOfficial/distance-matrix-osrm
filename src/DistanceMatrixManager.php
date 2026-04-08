<?php

namespace Haroun\DistanceMatrix;

use Illuminate\Support\Manager;
use Haroun\DistanceMatrix\Services\OsrmService;

class DistanceMatrixManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('distance-matrix.driver', 'osrm');
    }

    /**
     * Create an instance of the OSRM driver.
     *
     * @return \Haroun\DistanceMatrix\Services\OsrmService
     */
    public function createOsrmDriver(): OsrmService
    {
        return new OsrmService($this->config->get('distance-matrix.osrm'));
    }
}
