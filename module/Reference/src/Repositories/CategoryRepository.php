<?php
namespace Reference\Repositories;

use Core\Utils\Conditions;
use Core\Utils\Options;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Reference\Entity\Category;
use Reference\Entity\CategoryRoleRef;

class CategoryRepository extends AbstractReferenceRepository
{
    private $entity = '\Reference\Entity\Category';

    public function findByRole($roles)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->join('r.roles', 'roles')
            ->where('roles in ('.implode(',', $roles).')')
            ->andWhere(Conditions::jsonNotContains('r', Options::ARCHIVAL))
            ->orderBy('r.name', 'ASC');
        $categories = $qb->getQuery()->getResult();
        $result = [];
        foreach ($categories as $category) {
            $category->setName(mb_strtoupper($category->getName()));
            $result[] = $category;
        }
        return $result;
    }

    /**
     * Удалить отношение роли и категории
     * @param $categoryId
     * @throws ORMException
     */
    public function removeRoleRefByCategory($categoryId)
    {
        $refs = $this->getEntityManager()->getRepository(CategoryRoleRef::class)->findBy(['category' => $categoryId]);
        foreach ($refs as $ref) {
            $this->getEntityManager()->remove($ref);
        }
    }

    /**
     * Сохранить отношение роли и категории
     * @param $ref
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveCategoryRoleRef($ref)
    {
        $this->getEntityManager()->persist($ref);
        $this->getEntityManager()->flush();
    }

    /**
     * Поиск категории по наименованию
     * @param $name
     * @return Category
     * @throws NonUniqueResultException
     */
    public function getByName(string $name)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where("r.name LIKE '$name'");
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Поиск категории по ее алиасу
     * @param string $alias
     * @return Category
     * @throws NonUniqueResultException
     */
    public function getByAlias(string $alias)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where("r.alias = '$alias'");
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param null $request
     * @throws ORMException
     */
    public function save($row, $request = null): void
    {
        $row->setOption(Options::DEFAULT, $request->getPost(Options::DEFAULT));
        $row->setOption(Options::ARCHIVAL, $request->getPost(Options::ARCHIVAL));

        parent::save($row);
    }

    /**
     * Очистить существующую категорию по умолчанию
     * @throws DBALException
     */
    public function clearDef()
    {
        $conn = $this->getEntityManager()->getConnection();

        $query = <<<QUERY
           UPDATE cost_category SET options = JSON_REMOVE(options,
                JSON_UNQUOTE(
                    JSON_SEARCH(`options`, 'one', "def")
                )
           ) where options like '%def%';
QUERY;

        $conn->query($query);
    }
}
