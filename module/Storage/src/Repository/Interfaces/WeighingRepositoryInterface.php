<?php

namespace Storage\Repository\Interfaces;

interface WeighingRepositoryInterface
{
    public function getTableList(array $params): array;

    public function getWeighingByExportIdDepartmentDate($data);
}
