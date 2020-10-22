<?php

namespace Spare\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\CommentElement;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\EmployeeSpareElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SpareElement;
use Application\Form\Filter\SubmitElement;
use Application\Form\Filter\VehicleAllElement;
use DOMPDFModule\View\Model\PdfModel;
use Spare\Entity\Consumption;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * Class ConsumptionController
 * @package Spare\Controller
 *
 * @method FlashMessenger flashMessenger()
 * @method CurrentUser currentUser()
 */
class ConsumptionController extends SpecifiedSpareController
{
    use FilterableController;

    protected string $indexRoute = 'spareConsumption';

    /**
     * Index action
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);
        $params['warehouseId'] = $this->getWarehouseId();
        $consumptions = $this->services['spareConsumptionService']->findConsumptions($params);
        $totalQuantity = $this->services['spareConsumptionService']->getQuantity($consumptions);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'form' => $filterForm->getForm($params),
            'consumptions' => $consumptions,
            'params' => $this->routeParams,
            'isAdmin' => $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh(),
            'totalQuantity' => $totalQuantity
        ]);
    }

    /**
     * Add action
     */
    public function addAction()
    {
        $totals = $this->services['spareTotalService']->getTotals($this->getWarehouseId());
        $cancelUri = $this->url()->fromRoute($this->indexRoute, $this->routeParams);
        $saveUriParams = array_merge(['action' => 'save'], $this->routeParams);
        $saveUri = $this->url()->fromRoute($this->indexRoute, $saveUriParams);
        $employees = $this->services['employeeService']->getHasSpareJson();
        $vehicles = $this->services['vehicleService']->getVehicleJson();

        return new ViewModel([
            'jsonSpare' => json_encode($totals),
            'cancelUri' => $cancelUri,
            'saveUri' => $saveUri,
            'consumptions' => '{}',
            'employees' => $employees,
            'accesses' => json_encode($this->getPermissions()),
            'vehicles' => $vehicles,
            'roles' => json_encode($this->currentUser()->getRoleNames()),
        ]);
    }

    /**
     * Edit action
     * @throws
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $consumption = $this->services['spareConsumptionService']->find($id);

        if (empty($consumption)) {
            $this->flashMessenger()->addMessage('Списание не найдено');
            return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
        }

        $totals = $this->services['spareTotalService']->getTotals($this->getWarehouseId());

        $cancelUri = $this->url()->fromRoute($this->indexRoute, $this->routeParams);
        $saveUriParams = array_merge(['action' => 'update'], $this->routeParams);
        $saveUri = $this->url()->fromRoute($this->indexRoute, $saveUriParams);

        // Explanation: Доступные сотрудники
        $employees = $this->services['employeeService']->getHasSpareJson();
        $vehicles = $this->services['vehicleService']->getVehicleJson();

        return new ViewModel([
            'jsonSpare' => json_encode($totals),
            'cancelUri' => $cancelUri,
            'saveUri' => $saveUri,
            'consumptions' => json_encode($consumption),
            'employees' => $employees,
            'accesses' => json_encode($this->getPermissions()),
            'vehicles' => $vehicles,
            'roles' => json_encode($this->currentUser()->getRoleNames()),
        ]);
    }

    /**
     * Используется для сохранения данных при добавлении расхода
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $dataToSave = (array)$this->getRequest()->getPost();

            if (empty($dataToSave['positions'])) {
                return $this->responseError('Пустой запрос');
            }

            $positions = json_decode($dataToSave['positions'], true);
            if (empty($positions)) {
                return $this->responseError('Передана неверная структура json массива');
            }

            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $dataToSave['date']);
                $this->validateIfNoLaterInventory($this->getWarehouseId(), $dataToSave['date']);
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }

            $dataToSave['warehouse_id'] = $this->getWarehouseId();
            $dataToSave['isAdmin'] = $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh();
            $dataToSave['totals'] = $this->services['spareTotalService']->getTotals($this->getWarehouseId());

            if ($this->services['spareConsumptionService']->validateSave($dataToSave)) {
                $this->services['spareConsumptionService']->store($dataToSave);
                return $this->responseSuccess();
            } else {
                return $this->responseError('unprocessable entity');
            }
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * Используется для сохранения данных при редактировании расхода
     */
    public function updateAction()
    {
        if ($this->getRequest()->isPost()) {
            $dataToSave = (array)$this->getRequest()->getPost();

            if (empty($dataToSave['consumptionId'])) {
                return $this->responseError('Не передан идентификатор списания для редактирования');
            }

            if (empty($dataToSave['positions'])) {
                return $this->responseError('Пустой запрос');
            }

            $positions = json_decode($dataToSave['positions'], true);
            if (empty($positions)) {
                return $this->responseError('Передана неверная структура json массива');
            }

            $dataToSave['totals'] = $this->services['spareTotalService']->getTotals($this->getWarehouseId());
            $dataToSave['warehouse_id'] = $this->getWarehouseId();
            $dataToSave['isAdmin'] = $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh();

            $consumption = $this->services['spareConsumptionService']->find($dataToSave['consumptionId']);
            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $dataToSave['date'], $consumption);
                $this->validateIfNoLaterInventory($this->getWarehouseId(), $dataToSave['date']);
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }

            if ($this->services['spareConsumptionService']->validateUpdate($dataToSave, $consumption)) {
                $this->services['spareConsumptionService']->store($dataToSave);
                return $this->responseSuccess();
            } else {
                return $this->responseError('unprocessable entity');
            }
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * Экшн удаления записи
     *
     * @return \Zend\Http\Response
     * @throws
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        try {
            if (empty($id)) {
                throw new Exception('Не передан id');
            }
            /** @var Consumption $row */
            $row = $this->services['spareConsumptionService']->find($id);
            $this->accessValidateService->validateAccessForDays($this->depth, $row->getDate());

            $this->services['spareConsumptionService']->remove($id);
            $this->flashMessenger()->addMessage('Списание успешно удалено');
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
        }
        return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
    }

    /**
     * Экспорт списания в pdf
     */
    public function exportToPdfAction()
    {
        $consumptionId = $this->params()->fromRoute('id');
        $consumption = $this->services['spareConsumptionService']->find($consumptionId);

        $pdf = new PdfModel();
        $pdf->setOption('fileName', 'consumption');
        $pdf->setOption('paperSize', 'a4');
        $pdf->setOption('paperOrientation', 'portrait');

        $pdf->setVariables([
            'consumption' => $consumption,
        ]);

        return $pdf;
    }

    /**
     * Возвращает фильтр для indexAction
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(
            new CommentElement(
                new SpareElement(
                    new VehicleAllElement(
                        new EmployeeSpareElement(
                            new DateElement($this->entityManager)
                        )
                    )
                )
            )
        );
    }

    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'save' => $this->hasAccess(static::class, 'save'),
        ];
    }
}
