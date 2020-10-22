<?php
namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Repositories\OrderStatusRepository;

/**
 * Class OrderStatusService
 * @package Spare\Service
 * @method  OrderStatusRepository getRepository() Метод класса AbstractService
 */
class OrderStatusService extends AbstractService
{
    public function getOrderStatuses()
    {
        $statuses = $this->getRepository()->findAll();

        $result = [];
        foreach ($statuses as $status) {
            $result[$status->getAlias()] = $status;
        }

        return $result;
    }

    public function getStatusByAlias($alias)
    {
        return $this->getRepository()->findOneBy(['alias' => $alias]);
    }
}
