<?php

namespace Modules\Service;

use Application\Service\BaseService;

class ScheduledVehicleTripsService extends BaseService
{
    /**
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }

    protected $entity = '\Modules\Entity\MoveVehiclesEntity';
    protected $order = ['id' => 'DESC'];

    public function findBy($params)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where('r.completed = 0')
            ->orWhere('r.completed is null')
            ->orderBy('r.date', 'DESC');

        if (! empty($params['startdate'])) {
            $qb->andWhere(" r.date >= '{$params['startdate']}' ");
        } else {
            $qb->andWhere(" r.date >= '" . date('Y-m-01') . "' ");
        }

        if (! empty($params['enddate'])) {
            $qb->andWhere(" r.date <= '{$params['enddate']}' ");
        } else {
            $qb->andWhere(" r.date <= '" . date('Y-m-t') . "' ");
        }

        if (! empty($params['customerText'])) {
            $qb->andWhere("r.customer like '%" .$params['customerText']."%'");
        }

        if (isset($params['vehicle']) && $params['vehicle'] > 0) {
            $qb->andWhere('r.vehicle = ' .$params['vehicle']);
        }

        if (isset($params['department']) && $params['department'] > 0) {
            $qb->andWhere('r.department = ' .$params['department']);
        }
        return $qb->getQuery()->getResult();
    }
}
