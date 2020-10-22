<?php
namespace Reference\Service;

use Core\Entity\AbstractEntity;
use Core\Service\AbstractService;

/**
 * Class MetalService
 * @package Reference\Service
 */
class MetalService extends AbstractService
{

    protected array $order = ['group' => 'ASC','name' => 'ASC'];

    /**
     * Find default entity.
     * @return mixed
     */
    public function findDefault()
    {
        return $this->getRepository()->findOneBy(['def' => 1]);
    }

    /**
     * Save metal.
     * @param AbstractEntity $row
     * @param null  $request
     *
     */
    public function save(AbstractEntity $row, $request = null)
    {
        if ($row->getDef()) {
            $this->getRepository()->clearDef();
        }
        $this->getRepository()->save($row);
    }

    public function getListForJson(): array
    {
        $rows = $this->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => $row->getId(),
                'name' => $row->getName()
            ];
        }
        return $result;
    }
}
