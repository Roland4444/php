<?php
namespace Reference\Repositories;

use Core\Utils\Conditions;
use Core\Utils\Options;
use Doctrine\ORM\EntityRepository;
use Storage\Entity\Purchase;
use Reference\Entity\Customer;

class CustomerRepository extends EntityRepository
{
    private $entity = Purchase::class;
    private $customerEntity = Customer::class;

    /**
     * Поиск используется в формах для select
     *
     * @param $dep
     * @param array $excludes
     * @return array
     */
    public function findUsed($dep, $excludes = [])
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r,c')
            ->from($this->entity, 'r')
            ->join('r.customer', 'c')
            ->join('r.department', 'd')
            ->groupBy('c')
            ->orderBy('c.name')
            ->where("d.id = '$dep'")
            ->andwhere(Conditions::jsonNotContains('c', Options::ARCHIVE));
        $res = $qb->getQuery()->getResult();
        $arr = [];
        foreach ($res as $item) {
            if (in_array($item->getCustomer()->getId(), $excludes)) {
                continue;
            }
            $arr[] = $item->getCustomer();
        }
        return $arr;
    }

    /**
     * Используется в форме фильтров
     *
     * @param null $orderBy
     * @return mixed
     */
    public function findActive($orderBy = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from($this->customerEntity, 'c')
            ->where(Conditions::jsonNotContains('c', Options::ARCHIVE));

        if ($orderBy) {
            $orderKey = array_keys($orderBy)[0];
            $orderValue = $orderBy[$orderKey];

            $qb->orderBy("c.{$orderKey}", $orderValue);
        }

        return $qb->getQuery()->getResult();
    }

    public function getReference(int $id)
    {
        return $this->getEntityManager()->getReference($this->getClassName(), $id);
    }
}
