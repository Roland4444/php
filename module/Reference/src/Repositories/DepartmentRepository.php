<?php

namespace Reference\Repositories;

use Core\Repository\AbstractRepository;
use Core\Utils\Conditions;
use Core\Utils\Options;
use Reference\Entity\Department;

class DepartmentRepository extends AbstractRepository
{
    public function findWithoutMe($type = null, $departmentId = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from($this->getEntityName(), 'r')
            ->andWhere(Conditions::jsonNotContains('r', Options::CLOSED))
            ->andWhere(Conditions::jsonNotContains('r', Options::HIDE));
        if ($type) {
            $qb->andWhere("r.type = '$type'");
        }
        if ($departmentId) {
            $qb->andWhere("r.id <> $departmentId");
        }
        return $qb->getQuery()->getResult();
    }

    public function findOpened($isAdmin)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->getEntityName(), 'r')
            ->where(Conditions::jsonNotContains('r', Options::CLOSED));
        if (! $isAdmin) {
            $qb->andWhere(Conditions::jsonNotContains('r', Options::HIDE));
        }

        return $qb->getQuery()->getResult();
    }

    public function findChildren($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
            ->from($this->getEntityName(), 'd');
        if ($id) {
            $qb->andWhere($qb->expr()->eq('d.source', $id));
        } else {
            $qb->andWhere('d.source IS NOT NULL');
        }
        return $qb->getQuery()->getResult();
    }

    public function save($row, $request = null): void
    {
        $row->setOption(Options::CLOSED, $request->getPost(Options::CLOSED));
        $row->setOption(Options::HIDE, $request->getPost(Options::HIDE));

        parent::save($row);
    }
}
