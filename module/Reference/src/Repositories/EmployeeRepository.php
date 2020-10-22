<?php

namespace Reference\Repositories;

use Core\Utils\Options;
use Doctrine\ORM\EntityRepository;
use Reference\Entity\Employee;

class EmployeeRepository extends EntityRepository
{
    private $entity = Employee::class;

    public function findDrivers()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->entity, 'r')
            ->where("JSON_CONTAINS(r.options , '[\"" . Options::OPTIONS_DRIVER . "\"]') = 1");

        return $qb->getQuery()->getResult();
    }

    public function findConsumersOfSpares()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->entity, 'r')
            ->where("JSON_CONTAINS(r.options , '[\"" . Options::OPTIONS_SPARE . "\"]') = 1");

        return $qb->getQuery()->getResult();
    }
}
