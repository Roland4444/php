<?php

namespace Reference\Service;

use Application\Service\BaseService;
use Core\Utils\Options;
use Reference\Entity\Employee;

/**
 * Class EmployeeService
 *
 * @package Reference\Service
 */
class EmployeeService extends BaseService
{
    protected $order = ['id' => 'ASC'];

    public function __construct()
    {
        $this->setEntity(Employee::class);
    }

    /**
     * Получение json строки с данными о водителях и их лицензиях
     *
     * @return false|string
     */
    public function getLicense()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r');
        $results = $qb->getQuery()->getResult();
        $array = [];
        foreach ($results as $result) {
            $array[$result->getId()] = $result->getLicense();
        }

        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Обработка перед сохранением
     *
     * @param Employee $row
     * @param \Zend\Http\Request $request
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($row, $request = null)
    {
        $row->setOption(Options::OPTIONS_DRIVER, $request->getPost(Options::OPTIONS_DRIVER));
        $row->setOption(Options::OPTIONS_SPARE, $request->getPost(Options::OPTIONS_SPARE));
        return parent::save($row);
    }

    /**
     * Возвращает данные для выпадающего списка
     *
     * @return false|string
     */
    public function getHasSpareJson()
    {
        $employees = $this->em->getRepository($this->getEntity())->findConsumersOfSpares();
        if (empty($employees)) {
            return '[]';
        }

        $jsonEmployees = [];

        /** @var Employee $employee */
        foreach ($employees as $employee) {
            $jsonEmployees[$employee->getId()] = [
                'value' => $employee->getId(),
                'text' => $employee->getName(),
            ];
        }

        return json_encode($jsonEmployees, JSON_UNESCAPED_UNICODE);
    }
}
