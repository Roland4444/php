<?php

namespace Core\Traits;

trait RouteParams
{
    protected function getRouteId()
    {
        return $this->getEvent()->getRouteMatch()->getParam('id', null);
    }
}
