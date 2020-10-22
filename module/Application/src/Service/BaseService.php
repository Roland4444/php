<?php

namespace Application\Service;

use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BaseService implements FactoryInterface
{
    /**
     * @var EntityManager
     */
    protected $em;
    protected $dao;

    /**
     * Order by
     * @var array
     */
    protected $order = ['name' => 'asc'];
    protected $entity;
    protected $config;
    protected $filterParams = [
        'startdate',
        'enddate',
        'source',
        'dest',
        'bank',
        'department',
        'category',
        'comment',
        'trader',
        'type',
    ];

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new static($this);
        $service->setEntityManager($container->get('Doctrine\ORM\EntityManager'));
        return $service;
    }

    public function setEntityManager($entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param $entityClass
     * @internal param $entity
     */
    protected function setEntity($entityClass)
    {
        $this->entity = $entityClass;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setParams(array $params)
    {
        foreach ($params as $key => $param) {
            if (in_array($key, $this->filterParams)) {
                $this->$key = $param;
            }
        }
        return $this;
    }

    /**
     * Find entity by id
     *
     * @param $id
     *
     * @return mixed
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function find($id)
    {
        //TODO выпелить это. вводит в заблуждение
        if (! $id) {
            return $this->findAny();
        }
        return $this->em->find($this->entity, $id);
    }

    /**
     * Find any entity
     * @return mixed
     */
    private function findAny()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r');
        $res = $qb->getQuery()->getResult();
        if (count($res) === 0) {
            return null;
        }
        return $res[0];
    }

    /**
     * Find default entity
     * @return mixed
     */
    public function findDefault()
    {
        return $this->em->getRepository($this->entity)->findOneBy(['def' => 1]);
    }

    /**
     * Find all entity
     * @return mixed
     */
    public function findAll()
    {
        return $this->em->getRepository($this->entity)->findBy([], $this->order);
    }

    /**
     * Save entity
     *
     * @param $row
     * @param $request
     *
     * @return mixed
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save($row, $request = null)
    {
        $this->em->persist($row);
        $this->em->flush();
        return true;
    }

    /**
     * Remove entity
     *
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function remove($id)
    {
        if ($id) {
            $row = $this->em->find($this->entity, $id);
            $this->em->remove($row);
            $this->em->flush();
        }
    }

    public function getItemsForSelect()
    {
        return array_map(function ($item) {
            return [
                'value' => $item->getId(),
                'text' => $item->getName(),
                'default' => method_exists($item, 'getDef') && $item->getDef()
            ];
        }, $this->findAll());
    }

    /**
     * @param int $id
     * @return bool|Proxy|object|null
     * @throws ORMException
     */
    public function getReference(int $id)
    {
        return $this->em->getReference($this->entity, $id);
    }
}
