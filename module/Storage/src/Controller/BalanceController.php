<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Doctrine\ORM\EntityManager;
use Reference\Entity\Metal;
use Reference\Entity\MetalGroup;
use Reference\Service\DepartmentService;
use Reference\Service\MetalGroupService;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\ContainerItemService;
use Storage\Service\PurchaseService;
use Storage\Service\TransferService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class BalanceController
 * @package Storage\Controller
 * @method CurrentDepartment currentDepartment()
 * @method CurrentUser currentUser()
 */
class BalanceController extends AbstractActionController
{
    use FilterableController;
    protected string $indexRoute = 'balance';
    private const DATE_ZERO = '2000-01-01';

    private PurchaseService $purchaseService;
    private MetalGroupService $metalGroupService;
    private ContainerItemService $containerItemService;
    private TransferService $transferService;
    private DepartmentService $departmentService;
    private EntityManager $entityManager;

    /**
     * BalanceController constructor.
     * @param $entityManager
     * @param $services
     */
    public function __construct($entityManager, $services)
    {
        $this->entityManager = $entityManager;
        $this->purchaseService = $services[PurchaseService::class];
        $this->metalGroupService = $services[MetalGroupService::class];
        $this->containerItemService = $services[ContainerItemService::class];
        $this->transferService = $services[TransferService::class];
        $this->departmentService = $services[DepartmentService::class];
    }

    public function indexAction()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($this->currentDepartment()->isHide()) {
            die('');
        }

        $departmentId  = $currentDepartment ? $currentDepartment->getId() : null;

        $form = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $form->getFilterParams($this->indexRoute);

        if (! in_array($params['department'], [0, $departmentId]) && $this->currentUser()->getDepartment() === null) {
            return $this->redirect()->toRoute($this->indexRoute, ['department' => $params['department']]);
        }

        $rows = $this->getTotalWeight($params['startdate'], $params['enddate'], $departmentId);

        return new ViewModel([
            'rows' => $rows['rows'],
            'dep' => $departmentId,
            'total' => $rows['total'],
            'form' => $form->getForm($params),
            'route' => $this->indexRoute
        ]);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        if ($this->currentUser()->getDepartment()) {
            return new SubmitElement((new DateElement($this->entityManager)));
        }
        return new SubmitElement(new DepartmentElement(new DateElement($this->entityManager)));
    }

    /**
     * Получить полный вес
     * @param $dateFrom
     * @param $dateTo
     * @return int|mixed
     */
    private function getTotalWeight($dateFrom, $dateTo, $departmentId)
    {
        $departments = $this->departmentService->findChildren($departmentId);

        $subs = $this->containerItemService->getSubtracting($departments);

        $rows = [];
        $total = 0;

        $transferBalance = [
            'period' => $this->transferService->getBalance($dateFrom, $dateTo, $this->currentDepartment()->getId()),
            'full' => $this->transferService->getBalance(self::DATE_ZERO, $dateTo, $this->currentDepartment()->getId()),
        ];
        $purchaseWeightList = [
            'period' => $this->purchaseService->getWeightByMetal($dateFrom, $dateTo, $departmentId),
            'full' => $this->purchaseService->getWeightByMetal(self::DATE_ZERO, $dateTo, $departmentId)
        ];
        $shipmentWeightList = [
            'period' => $this->containerItemService->getActualByMetal($dateFrom, $dateTo, $departmentId),
            'full' => $this->containerItemService->getActualByMetal(self::DATE_ZERO, $dateTo, $departmentId)
        ];

        $groups = $this->metalGroupService->findAllWithMetals();
        /** @var MetalGroup[] $groups */
        foreach ($groups as $group) {
            $metals = [];
            $group_sum = 0;
            /** @var Metal $metal */
            foreach ($group->getMetals() as $metal) {
                $item = $this->calculateItem($metal, $transferBalance['period'], $purchaseWeightList['period'], $shipmentWeightList['period']);
                $itemFull = $this->calculateItem($metal, $transferBalance['full'], $purchaseWeightList['full'], $shipmentWeightList['full'], $subs);
                $item['balance'] = $itemFull['balance'];
                if (round($item['balance']) != 0) {
                    $metals[] = $item;
                }
                $group_sum += $item['balance'];
            }
            if (round($group_sum) != 0) {
                $rows[] = [
                    'name' => $group->getName(),
                    'metals' => $metals,
                    'sum' => $group_sum,
                    'dep' => $departmentId,
                ];
            }
            $total += $group_sum;
        }

        return [
            'rows' => $rows,
            'total' => $total
        ];
    }

    private function calculateItem(Metal $metal, $transferBalanceArray, $purchaseWeightList, $shipmentWeightList, $subs = []): array
    {
        $in = $purchaseWeightList[$metal->getId()] ?? 0;
        $out = $shipmentWeightList[$metal->getId()] ?? 0;

        $transferBalance = 0;
        if (! empty($this->currentDepartment()->getId()) && isset($transferBalanceArray[$metal->getId()])) {
            $row = $transferBalanceArray[$metal->getId()];
            $transferBalance = $row['in_weight'] - $row['out_weight'];
        }

        $sorByPort = array_key_exists($metal->getId(), $subs) ? $subs[$metal->getId()] : 0;
        $balance = $in - $out + $transferBalance + $sorByPort;

        return [
            'name' => $metal->getName(),
            'in' => $in,
            'out' => $out,
            'sub' => $sorByPort,
            'transfer' => $transferBalance,
            'balance' => $balance,
        ];
    }
}
