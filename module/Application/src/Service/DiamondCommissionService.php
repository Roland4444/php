<?php

namespace Application\Service;

use Finance\Entity\BankAccount;
use Finance\Entity\OtherExpense;
use Finance\Service\OtherExpenseService;
use Reference\Service\SettingsService;
use Reference\Entity\Settings;
use Finance\Service\BankService;
use Reference\Service\CategoryService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;

class DiamondCommissionService
{
    private const CATEGORY_RNKO = 'rnko';

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var OtherExpenseService
     */
    private $expenseService;

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var BankService
     */
    private $bankService;

    public function __construct($settingsService, $expenseService, $categoryService, $bankService)
    {
        $this->settingsService = $settingsService;
        $this->expenseService = $expenseService;
        $this->categoryService = $categoryService;
        $this->bankService = $bankService;
    }

    /**
     * Выполняет уменьшение суммы в управленческих расходах после удаления записи в расходах на металл
     *
     * @param $date
     * @param $commission
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function decrease($date, $commission)
    {
        $expense = $this->expenseService->getDiamondExpense($date);
        if (empty($expense)) {
            return;
        }

        $expenseMoney = $expense->getMoney();
        $money = bcsub($expenseMoney, $commission, 16);
        $expense->setMoney($money);

        if ($money > 0) {
            $this->expenseService->save($expense);
        } else {
            $this->expenseService->remove($expense->getId());
        }
    }

    /**
     * Выполняет увеличение суммы в управленческих расходах после добавления записи в расходах на металл
     *
     * @param $date
     * @param $commission
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function increase($date, $commission)
    {
        $expense = $this->expenseService->getDiamondExpense($date);
        if ($expense === null) {
            $expense = $this->createDiamondOtherExpense($date, $commission);
        } else {
            $money = $expense->getMoney() + $commission;
            $expense->setMoney($money);
        }
        $this->expenseService->save($expense);
    }

    /**
     * Возвращает комисию от суммы
     *
     * @param $money
     * @return float
     */
    public function calculateCommission(float $money): float
    {
        $setting = $this->settingsService->findByAlias(Settings::DIAMOND_COMMISSION);
        if (empty($setting) || empty($setting->getValue())) {
            return 0;
        }
        $settingValue = (float)$setting->getValue();
        $hundredPercent = 100;
        $commission = $money * $settingValue / $hundredPercent;
        $diamondMinCommission = 40;
        if ($commission < $diamondMinCommission) {
            return $diamondMinCommission;
        }
        return round($commission, 2);
    }

    /**
     * Создает и наполняет новую сущность
     *
     * @param string $date
     * @param $commission
     * @return OtherExpense
     * @throws NonUniqueResultException
     */
    private function createDiamondOtherExpense($date, $commission): OtherExpense
    {
        //todo Надо бы слать запрос рестом и получать массив вместо \Finance\Entity\OtherExpense чтобы убрать связь
        $entity = new OtherExpense();
        $entity->setMoney($commission);
        $entity->setDate($date);
        $entity->setRealdate($date);
        $entity->setComment(OtherExpenseService::COMMENT_RNKO);

        $category = $this->categoryService->getByAlias(self::CATEGORY_RNKO);
        $entity->setCategory($category);

        $bankAccount = $this->bankService->findByAlias(BankAccount::ALIAS_RNKO);
        $entity->setBank($bankAccount);

        return $entity;
    }
}
