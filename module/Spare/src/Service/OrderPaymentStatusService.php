<?php
namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Repositories\OrderPaymentStatusRepository;

/**
 * Class OrderPaymentStatusService
 * @package Spare\Service
 * @method  OrderPaymentStatusRepository getRepository() Метод класса AbstractService
 */
class OrderPaymentStatusService extends AbstractService
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
