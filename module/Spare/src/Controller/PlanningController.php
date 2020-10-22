<?php

namespace Spare\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\NumberElement;
use Application\Form\Filter\SpareElement;
use Application\Form\Filter\SparePlanningStatusElement;
use Application\Form\Filter\SubmitElement;
use Core\Service\AccessValidateService;
use Core\Traits\RestMethods;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use \Exception;
use Spare\Entity\Planning;
use Spare\Entity\PlanningItems;
use Spare\Entity\PlanningStatus;
use Spare\Service\PlanningItemsService;
use Spare\Service\PlanningService;
use Zend\Http\Response;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * Class PlanningController
 * @package Spare\Controller
 * @method  FlashMessenger flashMessenger()
 * @method CurrentUser currentUser()
 */
class PlanningController extends BaseSpareController
{
    use FilterableController;
    use RestMethods;

    protected $services;
    protected $entityManager;
    private $indexRoute = 'spare_planning';

    /** @var PlanningService */
    private $planningService;

    /** @var PlanningItemsService */
    private $planningItemService;
    private AccessValidateService $accessValidateService;
    protected int $depth = 14;

    /**
     * PlanningController constructor.
     * @param $entityManager
     * @param $services
     * @throws
     */
    public function __construct($entityManager, $services, $accessValidateService)
    {
        $this->entityManager = $entityManager;
        $this->accessValidateService = $accessValidateService;
        $this->services = $services;
        $this->planningService = $services['sparePlanningService'];
        $this->planningItemService = $services['planningItemService'];
    }

    /**
     * Index action
     *
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        $planning = $this->planningService->getTableListData($params);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'form' => $filterForm->getForm($params),
            'plannings' => $planning,
        ]);
    }

    /**
     * Add action
     * @return ViewModel
     */
    public function addAction()
    {
        $employees = $this->services['employeeService']->getHasSpareJson();
        $vehicles = $this->services['vehicleService']->getVehicleJson();
        return new ViewModel([
            'jsonSpare' => $this->services['spareService']->getSpareJson(),
            'planning' => '{}',
            'employees' => $employees,
            'vehicles' => $vehicles,
            'accesses' => json_encode($this->getPermissions()),
            'cancelUri' => $this->url()->fromRoute($this->indexRoute),
            'saveUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'save']),
        ]);
    }

    /**
     * Edit action
     *
     * @return Response|ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $planning = $this->planningService->find($id);
        $employees = $this->services['employeeService']->getHasSpareJson();
        $vehicles = $this->services['vehicleService']->getVehicleJson();

        return new ViewModel([
            'jsonSpare' => $this->services['spareService']->getSpareJson(),
            'planning' => $this->planningService->getDataFromPlanning($planning),
            'employees' => $employees,
            'vehicles' => $vehicles,
            'accesses' => json_encode($this->getPermissions()),
            'cancelUri' => $this->url()->fromRoute($this->indexRoute),
            'saveUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'save']),
        ]);
    }

    /**
     * Используется для сохранения данных при добавлении и сохранении заявки
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();
            $planningId = (int)$request->get('planningNumber');
            if (! empty($planningId)) {
                $planning = $this->planningService->find($planningId);
                if ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) {
                    $status = $this->services['sparePlanningStatusService']->getStatusByAlias($request->get('status'));
                    $planning->setStatus($status);
                }
            } else {
                $planning = new Planning();
                $status = $this->services['sparePlanningStatusService']->getStatusByAlias(PlanningStatus::ALIAS_NEW);
                $planning->setStatus($status);
            }

            $date = $request->get('date');

            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $date, $planning);
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }
            $planning->setDate($date);
            $planning->setComment($request->get('comment'));

            $positionsJson = $request->get('positions');
            $positions = json_decode($positionsJson);
            if (empty($positions)) {
                return $this->responseError('Empty Request');
            }
            $items = $this->prepareItems($positions);
            if (! empty($planning->getId())) {
                $oldItems = $planning->getItems();
                $orders = $this->services['orderItemsService']->findByPlanningItems($oldItems);
                if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) && $orders) {
                    return $this->responseError('Заявка в работе. Позиции не могут быть изменены.');
                }
                $this->services['planningItemService']->removeItems($oldItems);
            }
            $planning->setItems($items);
            $employeeId = intval($request->get('employee'));
            $employee = $employeeId > 0 ? $this->services['employeeService']->getReference($employeeId) : null;
            $planning->setEmployee($employee);
            $vehicleId = intval($request->get('vehicle'));
            $vehicle = $vehicleId ? $this->services['vehicleService']->getReference($vehicleId) : null;
            $planning->setVehicle($vehicle);
            $this->planningService->save($planning);
        }
        return $this->responseSuccess();
    }

    /**
     * Экшн удаления записи
     *
     * @return Response
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');

        try {
            if (empty($id)) {
                throw new Exception('Не передан id');
            }

            /** @var Planning $planning */
            $planning = $this->planningService->find($id);
            $this->accessValidateService->validateAccessForDays($this->depth, $planning->getDate());

            $planningItems = $planning->getItems();
            if (! empty($planningItems)) {
                $orderItems = $this->services['orderItemsService']->findByPlanningItems($planningItems);
            }

            if (! empty($orderItems)) {
                throw new Exception('Заявка в работе и не может быть удалена');
            }
            $this->planningService->remove($id);
            $this->flashMessenger()->addMessage('Заявка успешно удалена.');
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
        }
        return $this->redirect()->toRoute($this->indexRoute);
    }

    /**
     * Delete planning item
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function deleteItemAction()
    {
        $id = $this->params()->fromRoute('id');

        $planningItem = $this->planningItemService->find($id);

        if ($planningItem->isDeletable()) {
            $this->planningService->deletePlanningItem($planningItem);
        }

        return $this->redirect()->toRoute($this->indexRoute);
    }

    public function inWorkAction()
    {
        $id = $this->params()->fromRoute('id');
        $this->planningService->setStatus($id, PlanningStatus::ALIAS_IN_WORK);

        $this->flashMessenger()->addMessage('Статус заявки обновлен');

        return $this->redirect()->toRoute(
            $this->indexRoute,
            [ 'action' => 'index',],
            [ 'fragment' => 'planning' . $id ]
        );
    }

    /**
     * Prepare items.
     *
     * @param $positions
     *
     * @return array
     */
    private function prepareItems($positions)
    {
        $spares = $this->services['spareService']->findAll();
        $items = [];
        foreach ($positions as $position) {
            $id = property_exists($position, 'id') ? (int) $position->id : 0;
            $count = property_exists($position, 'countInPlanning') ? abs($position->countInPlanning) : 0;

            $entity = new PlanningItems();
            $entity->setQuantity($count);
            foreach ($spares as $spare) {
                if ($spare->getId() == $id) {
                    $entity->setSpare($spare);
                }
            }
            $items[] = $entity;
        }
        return $items;
    }

    /**
     * Возвращает фильтр для indexAction
     *
     * @param array
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(
            new SpareElement(
                new NumberElement(
                    new SparePlanningStatusElement(
                        new DateElement($this->entityManager)
                    )
                )
            )
        );
    }

    /**
     * Права доступа
     *
     * @return array
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'save' => $this->hasAccess(static::class, 'save'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'inwork' => $this->hasAccess(static::class, 'in-work'),
            'status' => $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh(),
        ];
    }
}
