<?php

namespace Spare\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Spare\Entity\Spare;
use Spare\Entity\Transfer;
use Spare\Service\SpareService;
use Zend\Http\Response;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * Class TransferController
 * @package Spare\Controller
 *
 * @method FlashMessenger flashMessenger()
 * @method CurrentUser currentUser()
 */
class TransferController extends SpecifiedSpareController
{
    use FilterableController;

    /**
     * @var string
     */
    protected string $indexRoute = 'spareTransfer';

    /**
     * Index action
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);
        $params['warehouseId'] = $this->getWarehouseId();

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'form' => $filterForm->getForm($params),
            'routParams' => $this->routeParams,
            'params' => $this->routeParams,
            'rows' => $this->services['spareTransferService']->findTransfers($params),
            'isAdmin' => $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh(),
        ]);
    }

    /**
     * Add action
     */
    public function addAction()
    {
        return new ViewModel([
            'jsonSpare' => json_encode($this->services['spareTotalService']->getTotals($this->getWarehouseId())),
            'cancelUri' => $this->url()->fromRoute($this->indexRoute, $this->routeParams),
            'saveUri' => $this->url()->fromRoute($this->indexRoute, array_merge(
                ['action' => 'save'],
                $this->routeParams
            )),
            'transfers' => '{}',
            'warehouses' => $this->services['warehouseService']->getWarehouseJson($this->getWarehouseId()),
            'accesses' => json_encode($this->getPermissions()),
            'roles' => json_encode($this->currentUser()->getRoleNames()),
        ]);
    }

    /**
     * Edit action
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $transfer = $this->services['spareTransferService']->find($id);

        if (empty($transfer)) {
            $this->flashMessenger()->addMessage('Переброска недоступна для редактирования');
            return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
        }
        if ($transfer->getSource()->getId() != $this->getWarehouseId()) {
            $this->flashMessenger()->addMessage('Переброска доступна для редактирования только в складе отправителя');
            return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
        }
        $spareId = $transfer->getSpare()->getId();
        $totals = $this->services['spareTotalService']->getTotals($this->getWarehouseId(), $spareId);
        $cancelUri = $this->url()->fromRoute($this->indexRoute, $this->routeParams);
        $saveUriParams = array_merge(['action' => 'update'], $this->routeParams);
        $saveUri = $this->url()->fromRoute($this->indexRoute, $saveUriParams);
        $transfers = $this->services['spareTransferService']->getDataFromTransfer($transfer);
        $warehouses = $this->services['warehouseService']->getWarehouseJson($this->getWarehouseId());
        return new ViewModel([
            'jsonSpare' => json_encode($totals),
            'cancelUri' => $cancelUri,
            'saveUri' => $saveUri,
            'transfers' => $transfers,
            'warehouses' => $warehouses,
            'accesses' => json_encode($this->getPermissions()),
            'roles' => json_encode($this->currentUser()->getRoleNames()),
        ]);
    }

    /**
     * Используется для сохранения данных при добавлении и сохранении заказа
     * @return Response
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();

            $positionsJson = $request['positions'];
            if (empty($positionsJson)) {
                return $this->responseError('Empty Request');
            }

            $positions = json_decode($positionsJson, true);
            if (empty($positions)) {
                return $this->responseError('Empty Request');
            }

            $warehouseId = (int) $request['warehouse'];
            if (empty($warehouseId)) {
                return $this->responseError('Не передан склад');
            }

            if ($warehouseId == $this->getWarehouseId()) {
                return $this->responseError('Склад эспорта не может быть складом импорта');
            }

            $transferData = $this->services['spareTransferService']->getData($positions);

            if (! is_array($transferData)) {
                return $this->responseError($transferData);
            }

            $spares = $this->services[SpareService::class]->findByIds(array_keys($transferData));

            if (count($positions) != count($spares)) {
                return $this->responseError('Неверно переданы параметры');
            }

            $totals = $this->services['spareTotalService']->getTotals($this->getWarehouseId());

            $warehouseSource = $this->services['warehouseService']->getReference($this->getWarehouseId());
            $warehouseDest = $this->services['warehouseService']->getReference($request['warehouse']);

            $transfers = [];
            foreach ($spares as $spare) { /**@var Spare $spare*/
                $id = $spare->getId();
                $transfer = new Transfer();
                $transfer->setSource($warehouseSource);

                if (empty($totals[$id])) {
                    return $this->responseError('Остатки не найдены');
                }

                $totalCount = $totals[$id]['total'];

                if ($totalCount < $transferData[$id]['count']) {
                    return $this->responseError('Переданное количество привышает остатки');
                }

                $date = $request['date'];
                try {
                    $this->accessValidateService->validateAccessForDays($this->depth, $date);
                    $this->validateIfNoLaterInventory($this->getWarehouseId(), $request['date']);
                } catch (\Exception $e) {
                    return $this->responseError($e->getMessage());
                }

                $transfer->setDate($date);
                $transfer->setQuantity($transferData[$id]['count']);
                $transfer->setSpare($spare);
                $transfer->setDest($warehouseDest);
                $transfers[] = $transfer;
            }

            $this->services['spareTransferService']->saveTransfers($transfers);
        }

        return $this->responseSuccess();
    }

    /**
     * Используется для сохранения данных при добавлении и сохранении заказа
     * @return Response
     */
    public function updateAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();

            $positionsJson = $request->get('positions');
            if (empty($positionsJson)) {
                return $this->responseError('Empty Request');
            }

            $positions = json_decode($positionsJson, true);
            if (empty($positions)) {
                return $this->responseError('Empty Request');
            }

            $transferId = (int) $request['transferId'];
            if (empty($transferId) ||  count($positions) != 1) {
                return $this->responseError('Неверно переданы параметры');
            }

            $warehouseId = (int) $request['warehouse'];
            if (empty($warehouseId)) {
                return $this->responseError('Не передан склад');
            }

            if ($warehouseId == $this->getWarehouseId()) {
                return $this->responseError('Склад эспорта не может быть складом импорта');
            }

            $transferData = $this->services['spareTransferService']->getData($positions);

            if (! is_array($transferData)) {
                return $this->responseError($transferData);
            }

            $spareId = array_keys($transferData)[0];
            $spare = $this->services[SpareService::class]->getReference($spareId);

            $totals = $this->services['spareTotalService']->getTotals($this->getWarehouseId(), $spareId);

            $warehouseDest = $this->services['warehouseService']->getReference($request->get('warehouse'));

            if (empty($totals[$spareId])) {
                return $this->responseError('Остатки не найдены');
            }

            $totalCount = $totals[$spareId]['total'];

            $transfer = $this->services['spareTransferService']->find($transferId); /**@var Transfer $transfer*/
            if (empty($transfer)) {
                return $this->responseError('Перемещение не найдено');
            }
            $totalCount += $transfer->getQuantity();

            $date = $request['date'];

            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $date, $transfer);
                $this->validateIfNoLaterInventory($this->getWarehouseId(), $request['date']);
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }

            if ($totalCount < $transferData[$spareId]['count']) {
                return $this->responseError('Переданное количество привышает остатки');
            }

            $transfer->setDate($date);
            $transfer->setQuantity($transferData[$spareId]['count']);
            $transfer->setSpare($spare);
            $transfer->setDest($warehouseDest);
            $transfers[] = $transfer;

            $this->services['spareTransferService']->saveTransfers($transfers);
        }
        return $this->responseSuccess();
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        try {
            if (empty($id)) {
                throw new \Exception('Не передан id');
            }
            /** @var Transfer $row */
            $row = $this->services['spareTransferService']->find($id);
            $this->accessValidateService->validateAccessForDays($this->depth, $row->getDate());

            $this->services['spareTransferService']->remove($id);
            $this->flashMessenger()->addMessage('Перемещение успешно удалено');
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
        }
        return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
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
