<?php

namespace Spare\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SpareElement;
use Application\Form\Filter\SpareSellerElement;
use Application\Form\Filter\SubmitElement;
use Core\Traits\RestMethods;
use Exception;
use Spare\Entity\Receipt;
use Spare\Entity\ReceiptItems;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * Class ReceiptController
 * @package Spare\Controller
 * @method CurrentUser currentUser()
 */
class ReceiptController extends SpecifiedSpareController
{
    use FilterableController;
    use RestMethods;

    /**
     * @var string
     */
    protected string $indexRoute = 'spareReceipt';

    /**
     * Index action
     * @return ViewModel
     * @throws Exception
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'form' => $filterForm->getForm($params),
            'receipts' => $this->services['spareReceiptService']->findByParams($params, $this->getWarehouseId()),
            'routParams' => $this->routeParams,
        ]);
    }

    /**
     * Add action
     * @return ViewModel
     */
    public function addAction()
    {
        $sellerOptions = $this->services['sellerService']->getItemsForSelect();
        $roles = $this->currentUser()->getRoleNames();

        return new ViewModel([
            'sellers' => json_encode($sellerOptions),
            'roles' => json_encode($roles),
            'routeParams' => $this->routeParams,
            'action' => 'save'
        ]);
    }

    /**
     * @return Response
     */
    public function getOrdersAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = json_decode($this->getRequest()->getContent(), true);
                $dateFrom = $params['dateFrom'];
                $dateTo = $params['dateTo'];
                $sellerId = (int)$params['seller'];

                if (empty($dateFrom) || empty($dateTo)) {
                    throw new Exception('Empty dateFrom or dateTo');
                }
                $orders = $this->services['orderService']->getByPeriodAndSellerId($dateFrom, $dateTo, $sellerId);
                return $this->responseSuccess(['result' => $orders]);
            } catch (Exception $e) {
                return $this->responseError($e->getMessage());
            }
        } else {
            return $this->responseError('Method not allowed');
        }
    }

    /**
     * Edit action
     * @return Response|ViewModel
     * @throws
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $receipt = $this->services['spareReceiptService']->find($id);

        if (empty($receipt)) {
            $this->flashMessenger()->addMessage('Приход недоступен для редактирования');
            return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
        }

        $sellerOptions = $this->services['sellerService']->getItemsForSelect();
        $roles = $this->currentUser()->getRoleNames();

        return new ViewModel([
            'receipt' => json_encode($receipt),
            'sellers' => json_encode($sellerOptions),
            'roles' => json_encode($roles),
            'routeParams' => $this->routeParams,
            'action' => 'update'
        ]);
    }

    /**
     * Используется для сохранения данных
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();
            $positionsJson = $request->get('positions');

            if (empty($positionsJson)) {
                return $this->responseError('Пустой запрос');
            }

            $positions = json_decode($positionsJson, true);
            if (empty($positions)) {
                return $this->responseError('Пустой запрос');
            }

            if (! $this->services['spareReceiptService']->isValidPositions($positions)) {
                return $this->responseError('Неверно переданы данные');
            }

            $orderItems = $this->services['orderItemsService']->findByPositions($positions);

            if (empty($orderItems)) {
                return $this->responseError('Неверно переданы данные');
            }

            try {
                $seller = $this->services['spareReceiptService']->getSeller($orderItems);
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }

            $warehouse = $this->services['warehouseService']->getReference($this->getWarehouseId());

            $date = $request['date'];
            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $date);
                $this->validateIfNoLaterInventory($this->getWarehouseId(), $request['date']);
            } catch (Exception $e) {
                return $this->responseError($e->getMessage());
            }

            $spareReceipt = new Receipt();
            $spareReceipt->setDate($date);
            $spareReceipt->setDocument($request['documentName']);
            $spareReceipt->setSeller($seller);
            $spareReceipt->setWarehouse($warehouse);

            [$planningItemIds, $orderIds] = $this->services['spareReceiptService']
                ->getIdsForUpdate($orderItems, $positions);

            $this->services['spareReceiptService']->fillingReceipt($orderItems, $positions, $spareReceipt);

            $this->services['spareReceiptService']->save($spareReceipt);
            $this->services['planningService']->setStatusPlanningByItemIds($planningItemIds);
            $this->services['orderService']->updateStatusOrderByIds($orderIds);

            return $this->responseSuccess();
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * Используется для сохранения данных при редактировании прихода
     */
    public function updateAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();
            $positionsJson = $request->get('positions');

            if (empty($positionsJson)) {
                return $this->responseError('Пустой запрос');
            }

            $positions = json_decode($positionsJson, true);
            if (empty($positions)) {
                return $this->responseError('Пустой запрос');
            }

            if (! $this->services['spareReceiptService']->isValidPositions($positions)) {
                return $this->responseError('Неверно переданы данные');
            }

            $orderItems = $this->services['orderItemsService']->findByPositions($positions);

            if (empty($orderItems)) {
                return $this->responseError('Неверно переданы данные');
            }

            try {
                $seller = $this->services['spareReceiptService']->getSeller($orderItems);
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }

            $receiptId = (int)$request['receiptId'];

            $spareReceipt = $this->services['spareReceiptService']->find($receiptId); /**@var Receipt $spareReceipt*/
            if (empty($spareReceipt)) {
                return $this->responseError('Поступление не найдено');
            }

            $date = $request['date'];
            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $date);
                $this->validateIfNoLaterInventory($this->getWarehouseId(), $request['date']);
            } catch (Exception $e) {
                return $this->responseError($e->getMessage());
            }

            $spareReceipt->setDate($date);
            $spareReceipt->setDocument($request['documentName']);
            $spareReceipt->setSeller($seller);
            $warehouse = $this->services['warehouseService']->getReference($this->getWarehouseId());
            $spareReceipt->setWarehouse($warehouse);


            [$idsPlanningItemsOld, $idsOrderOld] = $this->services['spareReceiptService']->getDateForUpdateStatus($spareReceipt);


            [$planningItemIds, $orderIds] = $this->services['spareReceiptService']
                ->getIdsForUpdate($orderItems, $positions);

            $this->services['spareReceiptService']->fillingReceipt($orderItems, $positions, $spareReceipt);

            $this->services['spareReceiptService']->save($spareReceipt);

            $idsPlanningItemsForUpdatePlanning = array_merge($idsPlanningItemsOld, $planningItemIds);
            $this->services['planningService']->setStatusPlanningByItemIds(array_unique($idsPlanningItemsForUpdatePlanning));

            $idsOrderForUpdateStatus = array_merge($idsOrderOld, $orderIds);
            $this->services['orderService']->updateStatusOrderByIds(array_unique($idsOrderForUpdateStatus));
            return $this->responseSuccess();
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * Экшн удаления записи
     *
     * @return Response
     * @throws
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        try {
            if (empty($id)) {
                throw new Exception('Не передан id');
            }
            $receipt = $this->services['spareReceiptService']->find($id); /**@var Receipt $receipt*/
            if (empty($receipt)) {
                throw new Exception('Не найден приход для удаления');
            }
            $this->accessValidateService->validateAccessForDays($this->depth, $receipt->getDate());
        } catch (Exception $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
        }

        $planningItemIds = [];
        $orderIds = [];
        foreach ($receipt->getItems() as $receiptItems) { /**@var ReceiptItems $receiptItems*/
            $orderItem = $receiptItems->getOrderItem();
            if (! empty($orderItem->getPlanningItem())) {
                $planningItemIds[] = $orderItem->getPlanningItem()->getId();
            }
            $orderIds[] = $orderItem->getOrder()->getId();
        }

        $this->services['spareReceiptService']->remove($id);
        $this->services['orderService']->updateStatusOrderByIds($orderIds);
        $this->services['planningService']->setStatusPlanningByItemIds($planningItemIds);
        $this->flashMessenger()->addMessage('Приход успешно удален');
        return $this->redirect()->toRoute($this->indexRoute, $this->routeParams);
    }

    /**
     * Возвращает фильтр для indexAction
     *
     * @param array
     * @return
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new SpareElement(new SpareSellerElement(new DateElement($this->entityManager))));
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
