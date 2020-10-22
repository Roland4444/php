<?php

namespace StorageTest\Controller\Mock;

trait MockParams
{
    private array $params = [];

    public function setRouteParam($paramName, $paramValue): void
    {
        $this->params[$paramName] = $paramValue;
    }

    protected function params()
    {
        return new class($this->params) {
            private array $params;
            public function __construct(array $params)
            {
                $this->params = $params;
            }
            public function fromRoute($paramName)
            {
                return $this->params[$paramName] ?? null;
            }
            public function fromQuery($paramName)
            {
                return $this->params[$paramName] ?? null;
            }
        };
    }
}
