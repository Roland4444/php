<?php

namespace Finance\Repositories;

use Core\Repository\AbstractRepository;

class BankAccountRepository extends AbstractRepository
{
    public function clearDef()
    {
        $query = $this->getEntityManager()->createQuery("UPDATE ".$this->getClassName()." s SET s.def = '0'");
        $query->execute();
    }
}
