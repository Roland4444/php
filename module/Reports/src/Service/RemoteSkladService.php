<?php
/**
 * Created by PhpStorm.
 * User: Миша
 * Date: 13.05.14
 * Time: 22:06
 */

namespace Reports\Service;

use Application\Service\BaseService;
use Interop\Container\ContainerInterface;
use Reference\Service\DepartmentService;
use Reports\Entity\RemoteSklad;

class RemoteSkladService extends BaseService
{
    protected $entity = '\Reports\Entity\RemoteSklad';
    protected $order = ['date' => 'ASC'];
    protected $startdate;
    protected $enddate;
    protected $department;
    protected $type;
    protected $total = ['sor' => null, 'weight' => null];
    protected $serviceLocator;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new static($this);
        $service->em = $container->get('Doctrine\ORM\EntityManager');
        $service->serviceLocator = $container;
        return $service;
    }

    public function setDates($startdate, $enddate)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
    }

    /**
     * @return mixed
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * @return mixed
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    public function getDepartmentName()
    {
        $departmentService = $this->serviceLocator->get(DepartmentService::class);
        $dep = $departmentService->find($this->department);
        return $dep->getName();
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    public function findAll()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->addOrderBy('r.date', 'DESC')
            ->addOrderBy('r.naklnumb', 'ASC')
            ->setMaxResults(1000);
        if ($this->startdate && ! empty($this->startdate)) {
            $qb->andWhere("r.date >= '" . $this->startdate . "'");
        } else {
            $qb->andWhere("r.date >= '" . date('Y-m-d') . "'");
        }

        if ($this->enddate && ! empty($this->startdate)) {
            $qb->andWhere(" r.date <= '" . $this->enddate . "'");
        } else {
            $qb->andWhere(" r.date <= '" . date('Y-m-d') . "'");
        }

        if ($this->department) {
            $qb->andWhere("r.sklad LIKE '%{$this->getDepartmentName()}%'");
        }
        if ($this->comment) {
            $qb->andWhere("r.transnumb LIKE '%{$this->comment}%'");
        }
        if ($this->type == 1 || ! $this->type) {
            // Экспорт
            $qb->andWhere("r.massa > 0 and r.transfer is null");
        } elseif ($this->type == 2) {
            // Переброска
            $qb->andWhere("r.massa < 0 or r.transfer is not null");
        }
        return $qb->getQuery()->getResult();
    }

    public function find($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where('r.id = ' . $id);
        return $qb->getQuery()->getSingleResult();
    }

    public function getTotalWeight()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(r.massa)')
            ->from($this->entity, 'r');
        if ($this->startdate && ! empty($this->startdate)) {
            $qb->andWhere("r.date >= '" . $this->startdate . "'");
        } else {
            $qb->andWhere("r.date >= '" . date('Y-m-d') . "'");
        }

        if ($this->enddate && ! empty($this->startdate)) {
            $qb->andWhere(" r.date <= '" . $this->enddate . "'");
        } else {
            $qb->andWhere(" r.date <= '" . date('Y-m-d') . "'");
        }

        if ($this->department) {
            $qb->andWhere("r.sklad LIKE '%{$this->getDepartmentName()}%'");
        }

        if ($this->type == 1 || ! $this->type) {
            // Экспорт
            $qb->andWhere("r.massa > 0 and r.transfer is null");
        } elseif ($this->type == 2) {
            // Переброска
            $qb->andWhere("r.massa < 0 or r.transfer is not null");
        }
        return $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getAvgSor()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('(sum(r.massa*100/(100-r.sor))-sum(r.massa))*100/(sum(r.massa*100/(100-r.sor)))')
            ->from($this->entity, 'r');
        if ($this->startdate && ! empty($this->startdate)) {
            $qb->andWhere("r.date >= '" . $this->startdate . "'");
        } else {
            $qb->andWhere("r.date >= '" . date('Y-m-d') . "'");
        }

        if ($this->enddate && ! empty($this->startdate)) {
            $qb->andWhere(" r.date <= '" . $this->enddate . "'");
        } else {
            $qb->andWhere(" r.date <= '" . date('Y-m-d') . "'");
        }

        if ($this->department) {
            $qb->andWhere("r.sklad LIKE '%{$this->getDepartmentName()}%'");
        }

        if ($this->type == 1 || ! $this->type) {
            // Экспорт
            $qb->andWhere("r.massa > 0 and r.transfer is null");
        } elseif ($this->type == 2) {
            // Переброска
            $qb->andWhere("r.massa < 0 or r.transfer is not null");
        }
        return $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);
    }

    public function findBy($date, $store, $waybill)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where("r.date = '{$date}'")
            ->andWhere("r.sklad = '{$store}'")
            ->andWhere("r.naklnumb = '{$waybill}'");
        return $qb->getQuery()->getResult();
    }

    public function getMassArray($dateFrom, $dateTo, $departmentName)
    {
        $is = $this->em->getRepository(RemoteSklad::class);
        $exportPurchaseList = $is->getMassArray($dateFrom, $dateTo, $departmentName);
        $result = [];
        foreach ($exportPurchaseList as $item) {
            $result[$item['metal']] = round($item['weight'], 2);
        }
        return $result;
    }

    public function findNumbersByTemplate($number)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r.transnumb')
            ->from($this->entity, 'r')
            ->distinct('r.transnumb')
            ->andWhere("r.transnumb LIKE '%{$number}%'");
        return $qb->getQuery()->getResult();
    }

    /**
     * Производит поиск переданного путевого листа
     *
     * @param string $date
     * @param string $departmentName
     * @param RemoteSklad $waybill
     * @return RemoteSklad|null
     */
    public function findWaybillInExport($date, $departmentName, $waybill)
    {
        $remoteSklad = $this->findBy($date, $departmentName, $waybill);

        if (0 == count($remoteSklad)) {
            return null;
        }
        return $remoteSklad[0];
    }
}
