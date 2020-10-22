<?php

namespace Storage\Service;

use Core\Service\AbstractService;
use Core\Traits\FormatNumbers;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Reference\Entity\Department;
use Reference\Service\ShipmentTariffService;
use Reference\Service\TraderService;
use Storage\Entity\ContainerItem;
use Storage\Entity\Shipment;
use Storage\Repository\ShipmentRepository;
use Zend\Http\PhpEnvironment\Request;
use Zend\Session\Container as SessionContainer;
use Zend\Stdlib\Parameters;

class ShipmentService extends AbstractService
{
    use FormatNumbers;

    private ContainerService $containerService;
    private TraderService $traderService;
    private ShipmentTariffService $tariffService;

    public function __construct($repository, $containerService, $traderService, $tariffService)
    {
        $this->containerService = $containerService;
        $this->traderService = $traderService;
        $this->tariffService = $tariffService;
        parent::__construct($repository);
    }

    protected function getRepository(): ShipmentRepository
    {
        return parent::getRepository();
    }

    public function findByParams(string $dateFrom, string $dateTo, int $departmentId = null, ?int $traderId = null)
    {
        return $this->getRepository()->findByParams($dateFrom, $dateTo, $departmentId, $traderId);
    }

    public function getDuplicate($post, $depId)
    {
        $result = $this->getRepository()->getDuplicate($post, $depId);
        return count($result) ? $result[0] : null;
    }

    public function getTotal($shipments): array
    {
        $result = [
            'weight' => 0,
            'actual' => 0,
            'sum' => 0,
            'count' => 0,
        ];
        /** @var Shipment $shipment */
        foreach ($shipments as $shipment) {
            $result['weight'] += $shipment->getWeight();
            $result['actual'] += $shipment->getRealWeight();
            $result['sum'] += $shipment->getSum();
            $result['count'] += count($shipment->getContainers());
        }
        return $result;
    }

    public function getTotalByMetal($shipments): array
    {
        $metals = [];
        $res = [];
        foreach ($shipments as $shipment) {
            $containers = $shipment->getContainers();
            foreach ($containers as $container) {
                $items = $container->getItems();
                foreach ($items as $item) {
                    $metalName = $item->getMetal()->getName();
                    if (! in_array($metalName, $metals)) {
                        $metals[] = $metalName;
                        $res[$metalName]['metal'] = $metalName;
                        $res[$metalName]['cost'] = $item->getCost();
                        $res[$metalName]['weight'] = $item->getWeight();
                        $res[$metalName]['real'] = $item->getRealWeight();
                        $res[$metalName]['sum'] = $item->getSum();
                        $res[$metalName]['weightWithoutPrice'] =
                            $item->getCost() == 0 ? $item->getRealWeight() : 0;
                    } else {
                        $res[$metalName]['cost'] += $item->getCost();
                        $res[$metalName]['weight'] += $item->getWeight();
                        $res[$metalName]['real'] += $item->getRealWeight();
                        $res[$metalName]['sum'] += $item->getSum();
                        if ($item->getCost() == 0) {
                            $res[$metalName]['weightWithoutPrice'] += $item->getRealWeight();
                        }
                    }
                }
            }
        }
        $result = [];
        foreach ($res as $row) {
            $weight = $row['real'] - $row['weightWithoutPrice'];
            if ($weight > 0) {
                $row['avg'] = $row['sum'] / $weight;
            } else {
                $row['avg'] = 0;
            }
            $result[] = $row;
        }
        return $result;
    }

    public function getTotalByGroup(string $dateFrom, string $dateTo): array
    {
        $groups = [];
        $res = [];
        $shipments = $this->findByParams($dateFrom, $dateTo);
        foreach ($shipments as $shipment) {
            $containers = $shipment->getContainers();
            foreach ($containers as $container) {
                $items = $container->getItems();
                foreach ($items as $item) {
                    $groupName = $item->getMetal()->getGroup()->getName();
                    if (! in_array($groupName, $groups)) {
                        if ($item->getCost() > 0) {
                            $groups[] = $groupName;
                            $res[$groupName]['group'] = $groupName;
                            $res[$groupName]['cost'] = $item->getCost();
                            $res[$groupName]['real'] = $item->getRealWeight();
                            $res[$groupName]['sum'] = $item->getSum();
                        }
                    } else {
                        if ($item->getCost() > 0) {
                            $res[$groupName]['cost'] += $item->getCost();
                            $res[$groupName]['real'] += $item->getRealWeight();
                            $res[$groupName]['sum'] += $item->getSum();
                        }
                    }
                }
            }
        }
        $result = [];
        foreach ($res as $row) {
            $result[] = ['group' => $row['group'], 'weight' => $row['real'],
                'price' => $row['sum'] / $row['real'],];
        }
        return $result;
    }

    /**
     * @param Shipment $row
     * @param null| Request $request
     *
     * @return mixed
     */
    public function save($row, $request = null)
    {
        $factoring = $request ? $request->getPost('factoring') : false;
        $row->setOption('factoring', $factoring);
        return parent::save($row);
    }

    public function findWithFactoring(string $dateFrom, string $dateTo)
    {
        return $this->getRepository()->findWithFactoring($dateFrom, $dateTo);
    }

    public function getSumFactoringRub($dateTo): ?float
    {
        return $this->getRepository()->getSumFactoringRub($dateTo);
    }

    public function getSumFactoringDol($dateTo): ?float
    {
        return $this->getRepository()->getSumFactoringDol($dateTo);
    }

    /**
     * @param Parameters $postData
     * @param Department $currentDepartment
     * @return Shipment
     * @throws ORMException
     */
    public function parseShipment(Parameters $postData, Department $currentDepartment): Shipment
    {
        $shipment = $this->getDuplicate($postData, $currentDepartment->getId());
        if ($shipment === null) {
            $shipment = new Shipment();
            $shipment->setDepartment($currentDepartment);
            $shipment->setDate($postData['date']);
            $shipment->setTrader($this->traderService->getReference($postData['trader']));
            $shipment->setTariff($this->tariffService->getReference($postData['tariff']));
            $shipment->setRate($postData['rate']);
        }

        $containers = $this->containerService->parseItems($postData['containers'], $shipment->getId(), $shipment->getDate(), $postData['tariff']);
        $shipment->addContainers($containers);
        if (count($shipment->getContainers()) === 0) {
            throw new InvalidArgumentException('Количество контейнеров не может быть ноль');
        }
        return $shipment;
    }

    public function getSummaryDataByItem(ContainerItem $item, int $departmentId): array
    {
        $container = $item->getContainer();
        $shipment = $container->getShipment();

        $item = [
            'metal' => $item->getMetal()->getName() . ' ' . $item->getComment(),
            'weight' => $this->formatNumber($item->getWeight()) . "&nbsp;кг.",
            'actual' => $this->formatNumber($item->getRealWeight()) . "&nbsp;кг.",
            'cost' => $this->formatNumber($item->getCost()). "&nbsp;р.",
            'dol' => $this->formatNumber($item->getCostDol()). "&nbsp;$",
            'sum' => $this->formatNumber($item->getSum()). "&nbsp;р.",
        ];
        $container = [
            'id' => $container->getId(),
            'weight' => $this->formatNumber($container->getWeight()) . "&nbsp;кг.",
            'actual' => $this->formatNumber($container->getRealWeight()) . "&nbsp;кг.",
            'sum' => $this->formatNumber($container->getSum()),
        ];
        $shipment = [
            'id' => $shipment->getId(),
            'weight' => $this->formatNumber($shipment->getWeight()) . "&nbsp;кг.",
            'actual' => $this->formatNumber($shipment->getRealWeight()) . "&nbsp;кг.",
            'sum' => $this->formatNumber($shipment->getSum()),
        ];

        $session = new SessionContainer('shipment');
        $dateFrom = $session['startdate'] ?? date('Y-m-01');
        $dateTo = $session['enddate'] ?? date('Y-m-t');
        $traderId = isset($session['trader']) ? (int)$session['trader'] : 0;


        $shipments = $this->findByParams($dateFrom, $dateTo, $departmentId, $traderId);
        $total = $this->getTotal($shipments);

        $totalView = [
            'weight' => $this->formatNumber($total['weight']) . "&nbsp;кг.",
            'actual' => $this->formatNumber($total['actual']) . "&nbsp;кг.",
            'sum' => $this->formatNumber($total['sum']),
        ];

        return [
            'shipment' => $shipment,
            'container' => $container,
            'item' => $item,
            'total' => $totalView,
        ];
    }

    public function findByItemId(int $id)
    {
        return $this->containerService->findByItemId($id);
    }
}
