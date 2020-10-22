<?php
namespace Reference\Repositories;

use Core\Utils\Conditions;
use Core\Utils\Options;
use Reference\Entity\Vehicle;

class VehicleRepository extends AbstractReferenceRepository
{

    private $entity = '\Reference\Entity\Vehicle';

    /**
     * Найти весь транспорт с признаком movable
     * @return mixed
     */
    public function findMovable()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->orderBy('r.name', 'ASC')
            ->andWhere(Conditions::jsonContains('r', Options::MOVABLE))
            ->andWhere(Conditions::jsonNotContains('r', Options::ARCHIVAL));
        return $qb->getQuery()->getResult();
    }

    /**
     * Получить все виды транспорта
     * @param array $params
     * @return array
     */
    public function getTableList(array $params): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->entity, 'r');


        if (! empty($params['name'])) {
            $qb->where('r.name like :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }

        if ($params['movable'] == Options::MOVABLE) {
            $qb->where(Conditions::jsonContains('r', Options::MOVABLE));
        } elseif ($params['movable'] == Options::NONMOVABLE) {
            $qb->where(Conditions::jsonNotContains('r', Options::MOVABLE));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Сохранить транспорт
     * @param Vehicle $row
     * @param null| \Zend\Http\PhpEnvironment\Request $request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($row, $request = null): void
    {
        $row->setOption(Options::MOVABLE, $request->getPost(Options::MOVABLE));
        $row->setOption(Options::ARCHIVAL, $request->getPost(Options::ARCHIVAL));

        parent::save($row);
    }
}
