<?php

namespace Spare\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SpareElement;
use Application\Form\Filter\NumberElement;
use Application\Form\Filter\SpareOrderStatusElement;
use Application\Form\Filter\PaymentStatusElement;
use Application\Form\Filter\SpareSellerElement;
use Application\Form\Filter\SubmitElement;
use Core\Service\AccessValidateService;
use Core\Traits\RestMethods;
use Exception;
use Spare\Entity\Order;
use Spare\Entity\OrderItems;
use Spare\Entity\OrderPaymentStatus;
use Zend\Http\Response;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class OrderController
 * @package Spare\Controller
 * @method  FlashMessenger flashMessenger()
 * @method CurrentUser currentUser()
 */
class OrderController extends BaseSpareController
{
    use FilterableController;
    use RestMethods;

    protected $entityManager;
    protected $services;

    /**
     * @var string
     */
    protected string $indexRoute = 'spareOrder';
    private AccessValidateService $accessValidateService;
    protected int $depth = 14;

    /**
     * OrderController constructor.
     * @param $entityManager
     * @param $authService
     * @param $services
     * @throws
     */
    public function __construct($entityManager, $services, $accessValidateService)
    {
        $this->entityManager = $entityManager;
        $this->accessValidateService = $accessValidateService;
        $this->services = $services;
        $this->dateService = $services['dateService'];
    }

    /**
     * Index action
     * @return ViewModel
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);
        $orders = $this->services['spareOrderService']->findOrders($params);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'form' => $filterForm->getForm($params),
            'orders' => $orders,
            'isAdmin' => $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh(),
            'totalAmount' => $this->services['spareOrderService']->getTotalAmount($orders)
        ]);
    }

    /**
     * Add action
     * @return ViewModel
     */
    public function addAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);
        $params['notReturnClose'] = true;

        return new ViewModel([
            'cancelUri' => $this->url()->fromRoute($this->indexRoute),
            'saveUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'save']),
            'roles' => json_encode($this->currentUser()->getRoleNames()),
            'dateFrom' => $params['startdate'],
            'dateTo' => $params['enddate'],
            'getPlanningUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'getPlanning']),
            'plannings' => $this->services['planningService']->getDataPlanning($params),
            'accesses' => json_encode($this->getPermissions()),
            'order' => '{}',
            'jsonSpare' => $this->services['spareService']->getSpareJson(),
            'sellers' => json_encode($this->services['sellerService']->getItemsForSelect()),
        ]);
    }

    /**
     * Используется для изменение выборки заявок
     *
     * @return JsonModel
     */
    public function getPlanningAction()
    {
        $status = 'ok';
        $error = '';
        $result = [];
        if ($this->getRequest()->isPost()) {
            try {
                $request = $this->getRequest()->getPost();
                $dateFrom = strtotime($request->get('dateFrom'));
                $dataTo = strtotime($request->get('dataTo'));

                if (empty($dateFrom) || empty($dataTo)) {
                    throw new \Exception('Empty dateFrom or dataTo');
                }

                $params = [
                    'startdate' => date('Y-m-d', $dateFrom),
                    'enddate' => date('Y-m-d', $dataTo),
                    'notReturnClose' => true,
                ];
                $result = $this->services['planningService']->getDataPlanning($params, false);
            } catch (\Exception $e) {
                $status = 'error';
                $error = 'Error. ' . $e->getMessage();
            }
        }
        return new JsonModel([
            'status' => $status,
            'error' => $error,
            'result' => $result
        ]);
    }

    /**
     * Edit action
     * @return Response|ViewModel
     * @throws
     */
    public function editAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        $id = $this->params()->fromRoute('id');
        $order = $this->services['spareOrderService']->find($id);

        if (empty($order)) {
            $this->flashMessenger()->addMessage('Заказ недоступен для редактирования');
            return $this->redirect()->toRoute($this->indexRoute);
        }

        $orderData = $this->services['spareOrderService']->getDataFromOrder($order);
        return new ViewModel([
            'cancelUri' => $this->url()->fromRoute($this->indexRoute),
            'saveUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'save']),
            'roles' => json_encode($this->currentUser()->getRoleNames()),
            'dateFrom' => $params['startdate'],
            'dateTo' => $params['enddate'],
            'getPlanningUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'getPlanning']),
            'plannings' => $this->services['planningService']->getDataPlanning($params),
            'order' => json_encode($orderData),
            'sellers' => json_encode($this->services['sellerService']->getItemsForSelect()),
            'jsonSpare' => $this->services['spareService']->getSpareJson(),
        ]);
    }

    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();

            $positionsJson = $request->get('positions');
            $positions = json_decode($positionsJson, true);

            if (! $this->services['spareOrderService']->isValidPositions($positions)) {
                return $this->responseError('Неверно переданы данные');
            }

            $seller = $this->services['sellerService']->find($request->get('provider'));
            $params = $request->toArray();
            $params['seller'] = $seller;

            /** @var Order $order */
            $order = $this->services['spareOrderService']->prepareOrder($params);
            if (empty($order)) {
                return $this->responseError('Приход не найден');
            }

            $date = $params['date'];

            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $date, $order);
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }

            $order->setDate($date);

            if ($order->getPaymentStatus()->getAlias() != OrderPaymentStatus::NOT_PAYMENT) {
                return $this->responseError('По данному заказу есть оплата. Редактирование невозможно.');
            }

            $updateItemIds = [];
            $insertItems = [];
            foreach ($positions as $position) {
                $orderItem = new OrderItems();
                $orderItem->setQuantity(abs($position['count']));
                $orderItem->setPrice(abs((float)$position['price']));
                $planning = $this->services['planningItemsService']->getReference($position['planningItemId']);
                $orderItem->setPlanningItem($planning);
                $spare = $this->services['spareService']->getReference($position['spareId']);
                $orderItem->setSpare($spare);
                if (! empty($position['isComposite'])) {
                    $orderItem->setSubQuantity($position['subCount']);
                }
                if (empty($position['itemId'])) {
                    $insertItems[] = $orderItem;
                } else {
                    $updateItemIds[$position['itemId']] = $orderItem;
                }
            }
            foreach ($order->getItems() as $item) {/**@var OrderItems $item*/
                if (in_array($item->getId(), array_keys($updateItemIds))) {
                    $item->setPrice($updateItemIds[$item->getId()]->getPrice());
                    $item->setQuantity($updateItemIds[$item->getId()]->getQuantity());
                    $item->setSpare($updateItemIds[$item->getId()]->getSpare());
                    $item->setSubQuantity($updateItemIds[$item->getId()]->getSubQuantity());
                } else {
                    $order->removeItem($item);
                }
            }
            $order->addItems($insertItems);

            $planningItemIds = array_map(function ($item) {
                return $item['planningItemId'];
            }, $positions);

            if (empty($order->getId())) {
                $this->services['spareOrderService']->save($order);
            } else {
                $this->services['spareOrderService']->merge($order);
            }

            $idsPlanningItemsForUpdatePlanning = $this->services['spareOrderService']->getPlanningIdFromOrder($order);
            $idsPlanningItemsForUpdatePlanning = array_merge($planningItemIds, $idsPlanningItemsForUpdatePlanning);
            if (! empty($idsPlanningItemsForUpdatePlanning)) {
                $this->services['planningService']->setStatusPlanningByItemIds(array_unique($idsPlanningItemsForUpdatePlanning));
            }
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
                $this->flashMessenger()->addMessage('Не передан id');
                return $this->redirect()->toRoute($this->indexRoute);
            }
            $order = $this->services['spareOrderService']->find($id);
            if (empty($order)) {
                throw new Exception('Не найден заказ для удаления');
            }
            $this->accessValidateService->validateAccessForDays($this->depth, $order->getDate());

            if ($order->getPaymentStatus()->getAlias() !== OrderPaymentStatus::NOT_PAYMENT) {
                throw new Exception('По данному заказу есть оплата. Удаление невозможно.');
            }

            $orderItems = $order->getItems();

            $planningItemIds = [];
            foreach ($orderItems as $orderItem) { /**@var OrderItems $orderItem*/
                if (! empty($orderItem->getItemsReceipt()->count())) {
                    throw new Exception('Удаление невозможно. По данному заказу уже есть приход.');
                }

                if (! empty($orderItem->getPlanningItem())) {
                    $planningItemIds[] = $orderItem->getPlanningItem()->getId();
                }
            }
            $this->services['spareOrderService']->remove($id);
            $this->services['planningService']->setStatusPlanningByItemIds($planningItemIds);
            $this->flashMessenger()->addMessage('Заказ успешно удален');
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
        }
        return $this->redirect()->toRoute($this->indexRoute);
    }

    /**
     * Возвращает фильтр для indexAction
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(
            new SpareElement(
                new PaymentStatusElement(
                    new SpareOrderStatusElement(
                        new NumberElement(
                            new SpareSellerElement(new DateElement($this->entityManager))
                        )
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
        ];
    }
}
