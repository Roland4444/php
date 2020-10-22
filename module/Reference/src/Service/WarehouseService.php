<?php

namespace Reference\Service;

use Core\Service\AbstractService;
use Core\Utils\Options;
use Doctrine\ORM\EntityManager;
use Reference\Entity\Warehouse;
use Reference\Repositories\WarehouseRepository;

/**
 * Class WarehouseService
 * @package Reference\Service
 * @method  WarehouseRepository getRepository() Метод класса AbstractService
 */
class WarehouseService extends AbstractService
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * WarehouseService constructor.
     *
     * @param             $repository
     * @param UserService $userService
     */
    public function __construct($repository, $userService)
    {
        parent::__construct($repository);
        $this->userService = $userService;
    }

    /**
     * @param Warehouse $row
     * @param null| \Zend\Http\PhpEnvironment\Request $request
     * @return mixed
     * @throws
     */
    public function save($row, $request = null)
    {
        if (! empty($request)) {
            $this->setUsers($row, $request);
            $row->setOption(Options::CLOSED, $request->getPost(Options::CLOSED));
        }

        $this->getRepository()->save($row);
    }

    /**
     * @param Warehouse $row
     * @param null| \Zend\Http\PhpEnvironment\Request $request
     * @throws
     */
    private function setUsers($row, $request)
    {
        $usersIds = $request->getPost('users');

        if (empty($usersIds)) {
            return;
        }
        $users = $this->userService->findByIds($usersIds);

        if (count($users) != count($usersIds)) {
            throw new \Exception('Неверно переданы данные пользователей для добавления');
        }

        $row->clearUsers();
        foreach ($users as $user) {
            $row->addUsers($user);
        }
    }

    /**
     * Возвращает данные для выпадающего списка
     *
     * @param $excludeId
     *
     * @return false|string
     */
    public function getWarehouseJson($excludeId)
    {
        $warehouses = $this->getRepository()->findAll();
        if (empty($warehouses)) {
            return '[]';
        }

        $jsonWarehouses = [];

        /** @var Warehouse $warehouse */
        foreach ($warehouses as $warehouse) {
            if ($warehouse->isClosed() || $excludeId == $warehouse->getId()) {
                continue;
            }
            $jsonWarehouses[$warehouse->getId()] = [
                'value' => $warehouse->getId(),
                'text' => $warehouse->getName(),
            ];
        }

        return json_encode($jsonWarehouses);
    }
}
