<?php

namespace FactoringTest\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class MockQueryBuilder extends QueryBuilder
{
    /**
     * Возвращает строку запроса и не выполняет сам запрос
     *
     * @return Query|object
     */
    public function getQuery()
    {
        $query = parent::getQuery();

        return new class($query)
        {
            /** @var Query */
            private $query;

            public function __construct($query)
            {
                $this->query = $query;
            }

            public function getResult($hydrationMode = null)
            {
                return $this->query->getDQL();
            }
        };
    }
}
