<?php

namespace Finance\Service;

use Core\Entity\AbstractEntity;
use Core\Service\AbstractService;
use Doctrine\ORM\ORMException;
use Finance\Entity\BankAccount;
use Finance\Repositories\BankAccountRepository;

/**
 * Class BankService
 * @package Finance\Service
 * @method BankAccountRepository getRepository()
 */
class BankService extends AbstractService
{
    /**
     * @return string
     */
    public function getEntity(): string
    {
        return BankAccount::class;
    }

    /**
     * @param AbstractEntity $row
     * @param null $request
     * @return void
     * @throws ORMException
     */
    public function save($row, $request = null)
    {
        if ($row->getDef()) {
            $this->getRepository()->clearDef();
        }
        $this->getRepository()->save($row);
    }

    /**
     * @return BankAccount
     */
    public function findCash(): BankAccount
    {
        return $this->getRepository()->findOneBy(['cash' => true]);
    }

    /**
     * @return BankAccount
     */
    public function findDiamond(): BankAccount
    {
        return $this->getRepository()->findOneBy(['diamond' => true]);
    }

    /**
     * @return BankAccount|null
     */
    public function findDefault(): ?BankAccount
    {
        return $this->getRepository()->findOneBy(['def' => 1]);
    }

    /**
     * @param string $number
     * @return BankAccount|null
     */
    public function findByNumber(string $number): ?BankAccount
    {
        return $this->getRepository()->findOneBy(['name' => $number]);
    }

    /**
     * @param string $name
     * @return BankAccount|null|object
     */
    public function findByAlias(string $alias)
    {
        return $this->getRepository()->findOneBy(['alias' => $alias]);
    }

    /**
     * @param bool $withClosed
     * @return array
     */
    public function findAll($withClosed = false): array
    {
        $condition = $withClosed ? [] : ['closed' => false];
        return $this->getRepository()->findBy($condition, ['name' => 'asc']);
    }

    /**
     * @return array
     */
    public function findWithoutCash(): array
    {
        return $this->getRepository()->findBy(['cash' => false, 'closed' => false]);
    }
}
