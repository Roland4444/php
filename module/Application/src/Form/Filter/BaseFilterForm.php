<?php

namespace Application\Form\Filter;

use Zend\Session\Container;

trait BaseFilterForm
{
    private function getSession($name)
    {
        return new Container($name);
    }

    private function getExistParams()
    {
        return [
            'startdate' => date('Y-m-01'),
            'enddate' => date('Y-m-t'),
            'source' => 0,
            'dest' => 0,
            'bank' => 0,
            'department' => 0,
            'category' => 0,
            'comment' => '',
            'trader' => 0,
            'customer' => 0,
            'customerText' => '',
            'vehicle' => 0,
            'type' => '',
            'owner' => null,
            'name' => '',
            'qr' => '',
            'movable' => null,
            'payment' => '',
            'inn' => '',
            'seller' => '',
            'traderParent' => 0,
            'spare' => 0,
            'employeeSpare' => 0,
            'planningStatus' => '',
            'orderStatus' => '',
            'number' => '',
            'paymentStatus' => '',
            'weightFrom' => '',
            'weightTo' => ''
        ];
    }

    public function getFilterParams($name)
    {
        $exist_params = $this->getExistParams();
        $ses = $this->getSession($name);

        $res = [];
        foreach ($exist_params as $key => $value) {
            if ($ses->$key || $ses->$key === '0') {
                $res[$key] = $ses->$key;
            } else {
                $res[$key] = $value;
            }
        }

        return $res;
    }

    public function reset($name)
    {
        $session = $this->getSession($name);
        $session->getManager()->getStorage()->clear($name);
    }

    public function setValues($values, $name)
    {
        $exist_params = $this->getExistParams();
        $ses = $this->getSession($name);
        foreach ($exist_params as $key => $value) {
            $ses->$key = $values[$key];
        }
    }
}
