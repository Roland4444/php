<?php

namespace Application\Form\Filter;

use Zend\Http\Request;
use Zend\Stdlib\RequestInterface;

trait FilterableController
{
    /**
     * Наполняет форму фильтра данными
     *
     * @param Request|RequestInterface $request
     * @param $name
     * @return mixed
     */
    protected function filterForm($request, $name)
    {
        $filter = $this->getFilterForm();
        if ($this->isClearFilter()) {
            $filter->reset($name);
        } elseif ($request->isPost()) {
            $filter->setValues($request->getPost(), $name);
        }
        return $filter;
    }

    /**
     * Определяет, следует ли оичтить форму фильтра
     *
     * @return boolean
     */
    protected function isClearFilter()
    {
        return $this->getEvent()->getRouteMatch()->getParam('clear', false);
    }
}
