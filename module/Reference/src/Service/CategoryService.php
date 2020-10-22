<?php

namespace Reference\Service;

use Core\Service\AbstractService;
use Core\Utils\Options;
use Reference\Entity\Category;
use Reference\Entity\CategoryRoleRef;
use Reference\Repositories\CategoryRepository;

/**
 * Class CategoryService
 * @package Reference\Service
 * @method CategoryRepository getRepository()
 */
class CategoryService extends AbstractService
{
    private $roleRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct($categoryRepository, $roleRepository)
    {
        parent::__construct($categoryRepository);
        $this->roleRepository = $roleRepository;
    }


    /**
     * {@inheritdoc}
     */
    public function save($row, $request = null)
    {
        if ($request->getPost(Options::DEFAULT)) {
            $this->getRepository()->clearDef();
        }

        $this->getRepository()->save($row, $request);
        $this->saveRoles($row, $request->getPost('roles'));
    }

    /**
     * Поиск категории по наименованию
     * @param $name
     * @return Category
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByName($name)
    {
        return $this->getRepository()->getByName($name);
    }

    /**
     * Поиск категории по ее алиасу
     * @param string $alias
     * @return Category
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByAlias($alias)
    {
        return $this->getRepository()->getByAlias($alias);
    }

    /**
     * @param Category $category
     * @param $roles
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function saveRoles($category, $roles)
    {
        $this->getRepository()->removeRoleRefByCategory($category->getId());

        //Проверка на переданные роли
        if (empty($roles)) {
            return;
        }

        foreach ($roles as $roleId) {
            $ref = new CategoryRoleRef();
            $ref->setCategory($category);

            $role = $this->roleRepository->find($roleId);
            $ref->setRole($role);

            $this->getRepository()->saveCategoryRoleRef($ref);
        }
    }

    /**
     * Find default entity from options json
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findDefaultByOption()
    {
        return $this->getRepository()->findDefaultByOption();
    }

    public function findByRole($roles)
    {
        return $this->getRepository()->findByRole($roles);
    }
}
