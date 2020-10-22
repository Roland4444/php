<?php

namespace Reports\Dao;

use Reports\Entity\RemoteSklad;

class RemoteSkladDao
{
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByDateDepartmentAndWaybill($date, $departmentName, $waybill)
    {
        return $this->entityManager->getRepository(RemoteSklad::class)
            ->findOneBy([
                'date' => $date,
                'sklad' => $departmentName,
                'naklnumb' => $waybill,
            ]);
    }

    /**
     * @param array $params
     * @return float
     */
    public function getAvgSor(array $params): ?float
    {
        return $this->entityManager
            ->getRepository(RemoteSklad::class)
            ->getAvgSor($params);
    }
}
