<?php

namespace Finance\Controller;

use Core\Traits\RestMethods;
use Finance\Entity\MetalExpense;
use Finance\Service\BankService;
use Finance\Service\MetalExpenseService;
use Reference\Service\CustomerService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MetalExpenseController extends AbstractActionController
{
    use RestMethods;

    private MetalExpenseService $metalExpenseService;
    private CustomerService $customerService;
    private BankService $bankService;

    public function __construct($metalExpenseService, $customerService, $bankService)
    {
        $this->metalExpenseService = $metalExpenseService;
        $this->customerService = $customerService;
        $this->bankService = $bankService;
    }

    public function getAction()
    {
        $id = $this->params()->fromRoute('id');
        $entity = $this->metalExpenseService->find($id);
        return $this->responseSuccess(['data' => $entity]);
    }

    public function indexAction()
    {
        return new ViewModel([
            'permissions' => json_encode([
                'add' => $this->hasAccess(static::class, 'add'),
                'delete' => $this->hasAccess(static::class, 'delete'),
                'edit' => $this->hasAccess(static::class, 'delete')
            ])
        ]);
    }

    public function listAction()
    {
        $dateFrom = $this->params()->fromQuery('date_from');
        $dateTo = $this->params()->fromQuery('date_to');
        $customerId = $this->params()->fromQuery('customer');
        $bankId = $this->params()->fromQuery('account');
        $result = $this->metalExpenseService->findByFilter($dateFrom, $dateTo, $customerId, $bankId);

        return $this->responseSuccess([
            'data' => $result['data'],
            'sum' => $result['sum']
        ]);
    }

    public function saveAction()
    {
        $content = $this->getRequest()->getContent();
        $data = json_decode($content);
        try {
            if (! empty($data->id)) {
                $expense = $this->metalExpenseService->find($data->id);
            } else {
                $expense = new MetalExpense();
            }
            $expense->setCustomer($this->customerService->getReference($data->customer));
            $expense->setBank($this->bankService->getReference($data->bank));
            $expense->setDate($data->date);
            $expense->setMoney($data->money);
            $this->metalExpenseService->save($expense);
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function deleteAction()
    {
        try {
            $id = $this->getRequest()->getPost('id');
            $this->metalExpenseService->remove($id);
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function customersAction()
    {
        $data = $this->customerService->findLegal();
        return $this->responseSuccess(['data' => $data]);
    }

    public function bankAccountsAction()
    {
        $data = $this->bankService->findAll();
        return $this->responseSuccess(['data' => $data]);
    }
}
