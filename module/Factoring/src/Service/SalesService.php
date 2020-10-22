<?php

namespace Factoring\Service;

use Storage\Facade\Storage;

class SalesService
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function findByPeriod($dateStart, $dateEnd): array
    {
        return $this->storage->findShipmentsWithFactoring($dateStart, $dateEnd);
    }

    public function getSumFactoring($dateEnd): ?float
    {
        return $this->storage->getSumShipmentWithFactoring($dateEnd);
    }
}
