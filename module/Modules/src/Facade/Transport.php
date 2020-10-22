<?php

namespace Modules\Facade;

use Modules\Entity\TransportIncas;
use Modules\Service\TransportIncasService;

class Transport
{
    private TransportIncasService $service;

    public function __construct(TransportIncasService $service)
    {
        $this->service = $service;
    }

    public function saveIncomeFromTransport($date, $money): void
    {
        $income = new TransportIncas();
        $income->setDate($date);
        $income->setMoney($money);
        $this->service->save($income);
    }
}
