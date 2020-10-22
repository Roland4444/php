<?php

namespace Storage\Service;

use Application\Exception\ServiceException;
use Core\Service\AbstractService;
use Finance\Service\MoneyToDepartmentService;
use Storage\Entity\MetalExpense;
use Storage\Entity\PurchaseDeal;
use Storage\Repository\Interfaces\MetalExpenseRepositoryInterface;

class MetalExpenseService extends AbstractService
{
    private $customerService;
    private $departmentService;
    private $weighingService;
    private $authService;
    /** @var MoneyToDepartmentService */
    private $moneyToDepartmentService;

    public function __construct(MetalExpenseRepositoryInterface $repository, $moneyToDepartmentService, $customerService, $departmentService, $weighingService, $authService)
    {
        parent::__construct($repository);
        $this->moneyToDepartmentService = $moneyToDepartmentService;
        $this->customerService = $customerService;
        $this->departmentService = $departmentService;
        $this->weighingService = $weighingService;
        $this->authService = $authService;
    }

    public function getTotalSumByDepartment($dateFrom, $dateTo, $departmentId, $customerId = null)
    {
        return $this->getRepository()->getTotalSumByDepartment($dateFrom, $dateTo, $departmentId, $customerId);
    }

    public function getSum($params = null)
    {
        return $this->getRepository()->getSum($params);
    }

    public function findAll($params = null)
    {
        return $this->getRepository()->findAll($params);
    }

    public function getSumByDeal(int $dealId): ?float
    {
        return $this->getRepository()->getSumByDeal($dealId);
    }

    /**
     * Pay a deal
     * @param PurchaseDeal $deal
     * @param $payment
     * @param $isDiamond
     * @throws ServiceException
     */
    public function payDeal(PurchaseDeal $deal, $payment, $isDiamond)
    {
        if ($payment <= 0) {
            throw new ServiceException('Выплата должна быть больше нуля');
        }

        $purchaseList = $deal->getPurchaseList();
        $date = date('Y-m-d');
        $customer = $purchaseList[0]->getCustomer();
        $department = $purchaseList[0]->getDepartment();

        $expense = new MetalExpense();
        $expense->setMoney($payment);
        $expense->setDepartment($department);
        $expense->setDate($date);
        $expense->setCustomer($customer);
        $expense->setFormal(0);
        $expense->setDeal($deal);
        $expense->setDiamond($isDiamond);

        $this->save($expense);
    }

    /**
     * Сохранение записи и создание job для выполнения добавления информации в управленчиские расходы
     *
     * @param MetalExpense $expense
     * @param null         $request
     * @return mixed
     * @throws
     */
    public function save($expense, $request = null)
    {
        if (empty($expense->getId()) && $expense->getDiamond()) {
            $expenseData = [
                'money' => $expense->getMoney(),
                'date' => $expense->getDate(),
                'department' => $expense->getDepartment(),

            ];
            $this->moneyToDepartmentService->createOrUpdateFromDiamond($expenseData);
        }
        $this->getRepository()->save($expense);
        return true;
    }

    public function payWeighing(array $data): void
    {
        if (empty($data['money'])) {
            throw new \Exception('Сумма оплаты не указана');
        }

        if ($this->checkIfWeighingIsTotallyPaid($data['weighingId'], $data['money'])) {
            throw new \Exception('Взвешивание было оплачено полностью ранее или сумма оплаты превышает требуемую сумму');
        }

        if (! $this->checkUserDepartment($data['departmentId'])) {
            throw new \Exception('Пользователь не состоит в запрашиваемом подразделении');
        }

        $metalExpense = new MetalExpense();
        $metalExpense->setDate(date('Y-m-d'));
        $metalExpense->setMoney($data['money']);
        $metalExpense->setFormal(0);
        $metalExpense->setDiamond($data['diamond']);
        $metalExpense->setDepartment(
            $this->departmentService->getReference($data['departmentId'])
        );
        $metalExpense->setWeighing(
            $this->weighingService->getReference($data['weighingId'])
        );
        $metalExpense->setCustomer(
            $this->customerService->getReference($data['customerId'])
        );

        $this->save($metalExpense);
    }

    private function checkIfWeighingIsTotallyPaid(int $weighingId, float $payableAmount): bool
    {
        $weighing = $this->weighingService->find($weighingId);

        $metalExpenses = $this->getRepository()->findBy([
            'weighing' => $weighingId
        ]);

        $paidAmount = 0;

        foreach ($metalExpenses as $expense) {
            $paidAmount += $expense->getMoney();
        }

        return $paidAmount + $payableAmount > ($weighing->getTotalPrice() + 50);
    }

    private function checkUserDepartment(int $departmentId): bool
    {
        $currentUser = $this->authService->getIdentity();
        if (! $currentUser->getDepartment()) {
            return true;
        }

        if ($currentUser->getDepartment()->getId() == $departmentId) {
            return true;
        }

        return false;
    }
}
