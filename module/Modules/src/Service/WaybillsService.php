<?php


namespace Modules\Service;

use Application\Service\BaseService;
use Modules\Entity\Waybill;

class WaybillsService extends BaseService
{
    protected $entity = '\Modules\Entity\Waybill';

    /**
     * Производит запрос на поиск всех путевых листов с установленными фильтрами
     * @param array $params
     * @param array $orders
     * @return mixed
     */
    public function findBy($params, $orders = ['dateStart' => 'ASC'])
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('d')->from($this->entity, 'd');


        if (! empty($params['vehicle'])) {
            $qb->andWhere("d.vehicle = '" . $params['vehicle'] . "'");
        }

        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $qb->andWhere("d.dateStart >= '" . $params['startdate'] . "' and d.dateStart <= '" . $params['enddate'] . "'");
        }

        foreach ($orders as $column => $direction) {
            $qb->addOrderBy('d.' . $column, $direction);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Выполняет подготовку данных и сохранение ссущности
     *
     * @param Waybill $row
     * @param null $request
     * @return mixed
     * @throws
     */
    public function save($row, $request = null)
    {
        $row->setCarNumber($row->getVehicle()->getNumber());
        $row->setLicense($row->getDriver()->getLicense());
        $row->setFuelConsumption($row->getVehicle()->getFuelConsumption());
        return parent::save($row);
    }

    /**
     * Возвращает максимальный номер путевого листа с увеличением на 1
     *
     * @return integer
     * @throws
     */
    public function getNextWaybillNumber()
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('MAX(d.waybillNumber) as maxNumber')
            ->from($this->entity, 'd')
            ->orderBy('d.id', 'DESC')->getQuery()->getOneOrNullResult();

        return 1 + (int)$result['maxNumber'];
    }
}
