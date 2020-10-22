<?php

namespace Reference\Service;

use Application\Service\BaseService;
use Core\Utils\Options;
use Reference\Entity\Customer;

/**
 * Class CustomerService
 *
 * @package Reference\Service
 */
class CustomerService extends BaseService
{
    /**
     * CustomerService constructor.
     */
    public function __construct()
    {
        $this->setEntity(Customer::class);
    }

    private function clearDef()
    {
        $query = $this->em->createQuery(
            "UPDATE " . $this->entity . " s SET s.def = '0'"
        );
        $query->execute();
    }

    /**
     * {@inheritdoc}
     *
     * @param Customer $row
     * @param null|\Zend\Http\PhpEnvironment\Request $request
     */
    public function save($row, $request = null)
    {
        if ($row->getDef()) {
            $this->clearDef();
        }

        if ($request->getPost(Options::ARCHIVE) !== null) {
            $row->setOption(Options::ARCHIVE, $request->getPost(Options::ARCHIVE));
        }

        $this->em->persist($row);
        $this->em->flush();
    }

    public function findLegal()
    {
        return $this->em->getRepository($this->entity)->findBy(
            ['legal' => 1],
            $this->order
        );
    }

    public function getCustomers()
    {
        return $this->em->getRepository(Customer::class)->findActive(['name' => 'asc']);
    }

    public function findUsed($departmentId) :array
    {
        return $this->em->getRepository(Customer::class)->findUsed($departmentId);
    }

    public function getTableList(array $params): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('r')
            ->from($this->entity, 'r')
            ->orderBy('r.name', 'ASC');

        if (! empty($params['name'])) {
            $qb->where('r.name like :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findByInn(string $inn): ?Customer
    {
        return $this->getEntityManager()->getRepository(Customer::class)->findOneBy(['inn' => $inn]);
    }
}
