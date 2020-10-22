<?php
namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Repositories\PlanningStatusRepository;

/**
 * Class PlanningStatusService
 * @package Spare\Service
 * @method  PlanningStatusRepository getRepository() Метод класса AbstractService
 */
class PlanningStatusService extends AbstractService
{
    public function getPlanningStatuses()
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
