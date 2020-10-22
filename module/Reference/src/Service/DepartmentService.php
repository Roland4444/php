<?php

namespace Reference\Service;

use Application\Service\BaseService;
use Core\Service\AbstractService;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Reference\Entity\Department;
use Reference\Repositories\DepartmentRepository;
use Zend\Http\PhpEnvironment\Request;

/**
 * Class DepartmentService
 *
 * @package Reference\Service
 * @method DepartmentRepository getRepository()
 */
class DepartmentService extends AbstractService
{
    protected array $order = ['id' => 'ASC'];

    public function findByAlias($alias)
    {
        return $this->getRepository()->findOneBy(['alias' => $alias]);
    }

    public function findDefault()
    {
        throw new \Exception('method not used');
    }

    /* find rows where $id department is source for result departments */
    public function findChildren($id)
    {
        return $this->getRepository()->findChildren($id);
    }

    /**
     * @param Department $row
     * @param null| Request $request
     * @return mixed
     * @throws ORMException
     */
    public function save($row, $request = null)
    {
        $this->getRepository()->save($row, $request);
    }

    public function getListForJson(): array
    {
        $rows = $this->getRepository()->findOpened(false);

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => $row->getId(),
                'name' => $row->getName(),
                'alias' => $row->getAlias(),
                'type' => $row->getType(),
            ];
        }
        return $result;
    }

    public function findOpened(bool $isAdmin): array
    {
        return $this->getRepository()->findOpened($isAdmin);
    }
}
