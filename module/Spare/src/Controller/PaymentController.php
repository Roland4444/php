<?php

namespace Spare\Controller;

use Core\Service\AccessValidateService;
use Core\Traits\RestMethods;
use Exception;
use Finance\Entity\OtherExpense;
use Spare\Entity\Order;
use Spare\Service\OrderService;
use OfficeCash\Service\OtherExpenseService;
use Zend\View\Model\ViewModel;

/**
 * Class PaymentController
 * @package Spare\Controller
 */
class PaymentController extends BaseSpareController
{
    use RestMethods;

    protected $services;
    private $indexRoute = 'sparePayment';

    /** @var OrderService */
    private $orderService;

    /** @var OtherExpenseService */
    private $storageExpenseService;
    private AccessValidateService $accessValidateService;
    protected int $depth = 14;

    /**
     * PlanningController constructor.
     * @param $services
     * @throws
     */
    public function __construct($services, $accessValidateService)
    {
        $this->services = $services;
        $this->accessValidateService = $accessValidateService;
        $this->orderService = $services['orderService'];
        $this->storageExpenseService = $services['storageExpenseService'];
    }

    /**
     * Index action
     *
     * @return ViewModel
     * @throws Exception
     */
    public function indexAction(): ViewModel
    {
        $sellers = $this->services['sellerService']->getItemsForSelect();
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $expensesList = $this->services['paymentService']->getBy($dateFrom, $dateTo);

        $searchData = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'seller' => '',
        ];

        $orders = $this->orderService->getNotPaid();

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'saveBindUrl' => $this->url()->fromRoute($this->indexRoute, ['action' => 'saveBind']),
            'removeBindUrl' => $this->url()->fromRoute($this->indexRoute, ['action' => 'removeBind']),
            'orderUrl' => $this->url()->fromRoute('spareOrder', ['action' => 'edit']),
            'getExpensesUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'json']),
            'searchData' => json_encode($searchData),
            'sellers' => json_encode($sellers),
            'expensesList' => json_encode($expensesList),
            'orders' => json_encode($orders),
        ]);
    }

    public function cashIndexAction()
    {
        $sellers = $this->services['sellerService']->getItemsForSelect();
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $expensesList = $this->services['paymentService']->getCashPayments($dateFrom, $dateTo);
        $orders = $this->orderService->getNotPaid(1);
        $orders = empty($orders) ? '{}' : json_encode($orders);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'saveBindUrl' => $this->url()->fromRoute($this->indexRoute, ['action' => 'save-cash-bind']),
            'removeBindUrl' => $this->url()->fromRoute($this->indexRoute, ['action' => 'remove-cash-bind']),
            'orderUrl' => $this->url()->fromRoute('spareOrder', ['action' => 'edit']),
            'getExpensesUri' => $this->url()->fromRoute($this->indexRoute, ['action' => 'cash-json']),
            'searchData' => json_encode([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]),
            'expensesList' => json_encode($expensesList),
            'orders' => $orders,
            'sellers' => json_encode($sellers),
        ]);
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

    /**
     * Используется для выбора данных платежей
     */
    public function jsonAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();
            $dateFrom = strtotime($request->get('dateFrom'));
            $dataTo = strtotime($request->get('dataTo'));

            $dateFrom = date('Y-m-d', $dateFrom);
            $dateTo = date('Y-m-d', $dataTo);
            $name = $request->get('name');
            $sellerId = $request->get('seller');

            $result = $this->services['paymentService']->getBy($dateFrom, $dateTo, $sellerId, $name);
            return $this->responseSuccess(['result' => $result]);
        }
        return $this->responseError('Method not allowed');
    }

    public function cashJsonAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();
            $dateFrom = strtotime($request->get('dateFrom'));
            $dataTo = strtotime($request->get('dataTo'));

            $dateFrom = date('Y-m-d', $dateFrom);
            $dateTo = date('Y-m-d', $dataTo);
            $name = $request->get('name');

            $result = $this->services['paymentService']->getCashPayments($dateFrom, $dateTo, $name);
            return $this->responseSuccess(['result' => $result]);
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * Используется для сохранения данных при добавлении связи
     */
    public function saveBindAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();
            try {
                $expense = $this->getExpenseFromRequest($request);
                $order = $this->getOrderFromRequest($request);
                $this->accessValidateService->validateAccessForDays($this->depth, $expense->getDate());

                $data = $this->services['paymentService']->editBind($expense, $order);
                return $this->responseSuccess(['data' => $data]);
            } catch (Exception $e) {
                return $this->responseError($e->getMessage());
            }
        }
        return $this->responseError('Method not allowed');
    }

    public function saveCashBindAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();
            try {
                $expenseId = (int)$request['expenseId'];
                if (empty($expenseId)) {
                    throw  new Exception('Не передан id заказа или id платежа');
                }
                $expense = $this->storageExpenseService->find($expenseId);
                if (empty($expense)) {
                    throw  new Exception('Платеж не найден');
                }
                $order = $this->getOrderFromRequest($request);
                $this->accessValidateService->validateAccessForDays($this->depth, $expense->getDate());
                $data = $this->services['paymentService']->editCashBind($expense, $order);
                return $this->responseSuccess(['data' => $data]);
            } catch (Exception $e) {
                return $this->responseError($e->getMessage());
            }
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * Используется для сохранения данных при удалении связи
     */
    public function removeBindAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();

            try {
                $expense = $this->getExpenseFromRequest($request);
                $order = $this->getOrderFromRequest($request);
                $this->accessValidateService->validateAccessForDays($this->depth, $expense->getDate());
                $data = $this->services['paymentService']->editBind($expense, $order, true);
                return $this->responseSuccess(['data' => $data]);
            } catch (Exception $e) {
                return $this->responseError($e->getMessage());
            }
        }
        return $this->responseError('Method not allowed');
    }

    public function removeCashBindAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest()->getPost();

            try {
                $expenseId = (int)$request['expenseId'];
                if (empty($expenseId)) {
                    throw  new Exception('Не передан id заказа или id платежа');
                }
                $expense = $this->storageExpenseService->find($expenseId);
                if (empty($expense)) {
                    throw  new Exception('Платеж не найден');
                }
                $order = $this->getOrderFromRequest($request);
                $this->accessValidateService->validateAccessForDays($this->depth, $expense->getDate());

                $data = $this->services['paymentService']->editCashBind($expense, $order, true);
                return $this->responseSuccess(['data' => $data]);
            } catch (Exception $e) {
                return $this->responseError($e->getMessage());
            }
        }
        return $this->responseError('Method not allowed');
    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    private function getExpenseFromRequest($request): OtherExpense
    {
        $expenseId = (int)$request['expenseId'];
        if (empty($expenseId)) {
            throw  new Exception('Не передан id заказа или id платежа');
        }
        $expense = $this->services['expenseService']->find($expenseId);
        if (empty($expense)) {
            throw  new Exception('Платеж не найден');
        }
        return $expense;
    }

    /**
     * @param $request
     * @return Order
     * @throws Exception
     */
    private function getOrderFromRequest($request): Order
    {
        $orderId = (int)$request['orderId'];
        if (empty($orderId)) {
            throw  new Exception('Не передан id заказа или id платежа');
        }
        $order = $this->services['orderService']->find($orderId);
        if (empty($order)) {
            throw  new Exception('Заказ не найден');
        }
        return $order;
    }
}
