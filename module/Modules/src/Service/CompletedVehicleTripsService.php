<?php

namespace Modules\Service;

use Application\Exception\ServiceException;
use Application\Service\BaseService;
use Doctrine\ORM\Query;
use Reports\Service\RemoteSkladService;

class CompletedVehicleTripsService extends BaseService
{
    protected $entity = '\Modules\Entity\MoveVehiclesEntity';
    protected $order = ['id' => 'DESC'];

    /**
     * @var RemoteSkladService
     */
    protected RemoteSkladService $remoteSkladService;
    private TransportIncasService $incasService;

    /**
     * @param $entityManager
     * @param $remoteSkladService
     * @param $incasService
     */
    public function __construct($entityManager, $remoteSkladService, $incasService)
    {
        $this->em = $entityManager;
        $this->remoteSkladService = $remoteSkladService;
        $this->incasService = $incasService;
    }

    public function getByPeriod(string $dateFrom, string $dateTo)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where('r.completed = 1 and r.date >= :dateFrom and r.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
        return $qb->getQuery()->getResult();
    }

    public function getMoneyBalance(string $initDate, string $dateTo)
    {
        return $this->getMoneySum($initDate, $dateTo) - $this->getIncas($dateTo);
    }

    private function getMoneySum(string $initDate, string $dateTo)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(r.payment)')
            ->from($this->entity, 'r')
            ->where('r.completed = 1 and r.date >= :dateFrom and r.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $initDate,
                'dateTo' => $dateTo,
            ]);
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getIncas(string $dateTo)
    {
        return $this->incasService->getMoneySum($dateTo);
    }

    /**
     * Get total sum by department
     * @param $dateFrom
     * @param $dateTo
     * @param $departmentId
     * @return float
     */
    public function getTotalSumByDepartment($dateFrom, $dateTo, $departmentId = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('SUM(r.payment)')
            ->from($this->entity, 'r')
            ->where('r.completed = 1')
            ->andWhere("r.date >='".$dateFrom."' and r.date<='".$dateTo."'");
        if ($departmentId) {
            $qb->andWhere("r.moneyDepartment = ".$departmentId);
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function findBy($params)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('m, r')
            ->from($this->entity, 'm')
            ->leftJoin('m.remoteSklad', 'r')
            ->where('m.completed = 1')
            ->orderBy('m.date', 'DESC');

        if (! empty($params['startdate'])) {
            $qb->andWhere(" m.date >= '{$params['startdate']}' ");
        } else {
            $qb->andWhere(" m.date >= '" . date('Y-m-01') . "' ");
        }

        if (! empty($params['enddate'])) {
            $qb->andWhere(" m.date <= '{$params['enddate']}' ");
        } else {
            $qb->andWhere(" m.date <= '" . date('Y-m-t') . "' ");
        }

        if (! empty($params['customerText'])) {
            $qb->andWhere("m.customer like '%" .$params['customerText']."%'");
        }

        if ($params['vehicle'] > 0) {
            $qb->andWhere('m.vehicle = ' .$params['vehicle']);
        }

        if ($params['department'] > 0) {
            $qb->andWhere('m.department = ' .$params['department']);
        }

        if (! empty($params['weightFrom'])) {
            $qb->andWhere("r.massa > :weightFrom")
                ->setParameter('weightFrom', (int)($params['weightFrom'] * 1000));
        }

        if (! empty($params['weightTo'])) {
            $qb->andWhere("r.massa <= :weightTo")
                ->setParameter('weightTo', (int)($params['weightTo'] * 1000));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Получеем данные для путевого листа по его параметрам(id Водителя и дат )
     * !!Отсортированный по дате!!
     *
     * @param $params
     *
     * @return mixed
     * @throws ServiceException
     */
    public function findTripsForWaybill($params)
    {
        $required = ['startDate', 'endDate', 'driver', 'vehicle'];
        foreach ($required as $paramName) {
            if (empty($params[$paramName])) {
                throw new ServiceException('Не указан обязательный параметр ' . $paramName);
            }
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where('r.completed = 1')
            ->andWhere(" r.date >= '{$params['startDate']}' ")
            ->andWhere(" r.date <= '{$params['endDate']}' ")
            ->andWhere(" r.driver = '{$params['driver']}' ")
            ->andWhere(" r.vehicle = '{$params['vehicle']}' ")
            ->andWhere(" r.distance <> 0 ")
            ->andWhere(" r.distance IS NOT NULL ")
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.departureTime', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * @param \Modules\Entity\MoveVehiclesEntity $row
     * @param \Zend\Http\PhpEnvironment\Request  $request
     *
     * @return mixed
     * @throws ServiceException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($row, $request = null)
    {
        $waybill = $request->getPost('waybill');
        if (strlen($waybill) == 0) {
            $row->setRemoteSklad(null);
            return parent::save($row, $request);
        }
        $remoteSklad = $this->remoteSkladService->findWaybillInExport(
            $row->getDate(),
            $row->getDepartment()->getName(),
            $waybill
        );
        if (! $remoteSklad) {
            throw new ServiceException('Такой накладной нет в базе.');
        }
        $row->setRemoteSklad($remoteSklad);
        return parent::save($row, $request);
    }
}
