<?php

namespace Spare\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Exception;
use Spare\Entity\Inventory;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * Class InventoryController
 * @package Spare\Controller
 * @method CurrentUser currentUser()
 */
class InventoryController extends SpecifiedSpareController
{
    use FilterableController;

    protected string $indexRoute = 'spareInventory';

    /**
     * Index action
     *
     * @return ViewModel
     * @throws Exception
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);
        $params['warehouseId'] = $this->getWarehouseId();
        $inventoryList = $this->services['inventoryService']->getTableListData($params);
        $lastInventory = $this->services['inventoryService']->getLastInventory($this->getWarehouseId());

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'form' => $filterForm->getForm($params),
            'routParams' => $this->routeParams,
            'inventories' => $inventoryList,
            'lastInventory' => $lastInventory,
            'isAdmin' => $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh(),
        ]);
    }

    /**
     * @return Response|ViewModel
     */
    public function addAction()
    {
        $totals = $this->services['totalService']->getTotals($this->getWarehouseId());
        usort($totals, function ($a, $b) {
            return $a['text'] <=> $b['text'];
        });
        $saveUriParams = array_merge(['action' => 'save'], $this->routeParams);
        $saveUri = $this->url()->fromRoute($this->indexRoute, $saveUriParams);
        $cancelUri = $this->url()->fromRoute($this->indexRoute, $this->routeParams);

        return new ViewModel([
            'spareTotal' => json_encode($totals),
            'spareList' => $this->services['spareService']->getSpareJson(),
            'cancelUri' => $cancelUri,
            'inventory' => '{}',
            'saveUri' => $saveUri,
            'enableSaving' => 1,
            'roles' => json_encode($this->currentUser()->getRoleNames()),
        ]);
    }

    /**
     * Edit action
     * @return \Zend\Http\Response|ViewModel
     * @throws
     */
    public function editAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);
        $params['warehouseId'] = $this->getWarehouseId();

        $id = $this->params()->fromRoute('id');
        $inventory = $this->services['inventoryService']->find($id);

        if (empty($inventory)) {
            $this->flashMessenger()->addMessage('Инвентаризация не найдена.');
            return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
        }

        $enableSaving = (int)$this->services['inventoryService']->isLastInventory($this->getWarehouseId(), $id);

        $saveUriParams = array_merge(['action' => 'update'], $this->routeParams);
        $saveUri = $this->url()->fromRoute($this->indexRoute, $saveUriParams);
        $cancelUri = $this->url()->fromRoute($this->indexRoute, $this->routeParams);

        return new ViewModel([
            'spareTotal' => '[]',
            'spareList' => $this->services['spareService']->getSpareJson(),
            'cancelUri' => $cancelUri,
            'inventory' => $inventory->toJson(),
            'saveUri' => $saveUri,
            'enableSaving' => $enableSaving,
            'roles' => json_encode($this->currentUser()->getRoleNames()),
        ]);
    }

    /**
     * Экшн удаления записи
     *
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $isLastInventory = $this->services['inventoryService']->isLastInventory($this->getWarehouseId(), $id);
        if (! $isLastInventory) {
            $this->flashMessenger()->addMessage('Инвентаризация не является последней. Удаление недоступно');
        }

        $row = $this->services['inventoryService']->find($id);
        if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) && $row->getDate() != date('Y-m-d')) {
            $this->flashMessenger()->addMessage('Нет прав на удаление данной инвентаризации');
            return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
        }

        $this->services['inventoryService']->remove($id);
        $this->flashMessenger()->addMessage('Инвентаризация успешно удалена');
        return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
    }

    /**
     * Save action
     *
     * @return ViewModel
     * @throws Exception
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();

            $date = $request['date'];
            if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
                $date = date('Y-m-d');
            }

            $isNextDateInventory = $this->services['inventoryService']->isNextDate(
                $this->getWarehouseId(),
                $date
            );

            if (! $isNextDateInventory) {
                return $this->responseError('Дата инвентаризации должна отличаться от последней в базе');
            }

            if (! $this->services['totalService']->checkIfInventoryIsAvailable($this->getWarehouseId(), $date)) {
                return $this->responseError('Инвентаризация невозможна так как на эту дату проводились операции по приходам, расходам или трансферам');
            }

            $warehouse = $this->services['warehouseService']->getReference($this->getWarehouseId());

            $inventory = new Inventory();
            $inventory->setWarehouse($warehouse);
            $inventory->setDate($date);

            $positions = $this->services['inventoryService']->getPositions($request);

            $this->services['inventoryService']->addingInventoryPositions($positions, $inventory);
            $this->services['inventoryService']->save($inventory);

            return $this->responseSuccess();
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * update action
     *
     * @return Response
     * @throws Exception
     */
    public function updateAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();

            $positions = $this->services['inventoryService']->getPositions($request);

            $inventory = $this->services['inventoryService']->find($request['inventoryId']);

            if (empty($inventory)) {
                return $this->responseError('Инвентаризация не найдена');
            }

            $date = $request['date'];
            if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
                $date = date('Y-m-d');
                if ($inventory->getDate() != $date) {
                    return $this->responseError('Инвентаризация находится вне диапазона разрешенных прав.');
                }
            }

            $warehouseId = $this->getWarehouseId();
            if ($warehouseId != $inventory->getWarehouse()->getId()) {
                return $this->responseError('Инвентаризация не относится к выбранному складу');
            }

            $isLastInventory = $this->services['inventoryService']->isLastInventory($warehouseId, $inventory->getId());

            if (! $isLastInventory) {
                return $this->responseError('Инвентаризация не является последней. Редактирование недоступно.');
            }

            if (strtotime($date) < strtotime($inventory->getDate())) {
                $lastInventory = $this->services['inventoryService']->getLastInventory($warehouseId);

                if (! empty($lastInventory)
                    && ($lastInventory->getId() != $inventory->getId())
                    && strtotime($lastInventory->getDate()) >= strtotime($date)
                ) {
                    return $this->responseError('Неверно указана дата. Она должна отличаться от последней в базе.');
                }
            }

            $inventory->setDate($date);

            $this->services['inventoryService']->fillingItems($positions, $inventory);
            $this->services['inventoryService']->save($inventory);

            return $this->responseSuccess();
        }
        return $this->responseError('Method not allowed');
    }


    /**
     * Возвращает фильтр для indexAction
     *
     * @param array
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new DateElement($this->entityManager));
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
        ];
    }
}
