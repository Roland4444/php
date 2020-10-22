<?php
/**
 * Created by PhpStorm.
 * User: kostyrenko
 * Date: 05.02.2019
 * Time: 17:15
 */

namespace ModuleTest\Controller;

use Modules\Controller\MoveVehiclesController;

class MockMoveVehiclesController extends MoveVehiclesController
{
    protected $hasAccess = true;

    public $mockAccessValidate = null;
    private $mockCurrentUser;

    public function setCurrentUser($mock)
    {
        $this->mockCurrentUser = $mock;
    }

    protected function currentUser()
    {
        return $this->mockCurrentUser;
    }

    protected function getEditForm()
    {
    }

    public function hasAccess($a, $b)
    {
        return $this->hasAccess;
    }

    public function setHasAccess($param)
    {
        $this->hasAccess = $param;
    }

    public function accessValidate(string $date, $limit)
    {
        if ($this->mockAccessValidate !== null) {
            return $this->mockAccessValidate;
        }
        return parent::accessValidate($date, $limit);
    }

    public function getTableListData($params)
    {
        return parent::getTableListData($params);
    }

    public function getFilterForm()
    {
        return parent::getFilterForm();
    }

    public function getPermissions()
    {
        return parent::getPermissions();
    }

    public function checkAccessToEdit($entity)
    {
        return parent::checkAccessToEdit($entity);
    }

    public function checkDataForEdit(array $data)
    {
        return parent::checkDataForEdit($data);
    }
}
