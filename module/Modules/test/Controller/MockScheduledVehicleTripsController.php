<?php

namespace ModuleTest\Controller;

use Modules\Controller\ScheduledVehicleTripsController;

class MockScheduledVehicleTripsController extends ScheduledVehicleTripsController
{
    protected $hasAccess = true;

    public function getCreateForm()
    {
        return parent::getEditForm();
    }

    public function getEditForm()
    {
        return parent::getEditForm();
    }

    public function checkDataForEdit(array $param)
    {
        return parent::checkDataForEdit($param);
    }

    public function getEntityForEdit($id)
    {
        return parent::getEntityForEdit($id);
    }

    public function checkDataForCreate(array $data)
    {
        return parent::checkDataForCreate($data);
    }

    public function setIsMoveVehiclesCompleteForm()
    {
        $this->isMoveVehiclesCompleteForm = true;
    }

    public function getIsMoveVehiclesCompleteForm()
    {
        return $this->isMoveVehiclesCompleteForm;
    }

    public function hasAccess()
    {
        return $this->hasAccess;
    }

    public function setHasAccess($param)
    {
        $this->hasAccess = $param;
    }

    public function getIndexRout()
    {
        return $this->indexRoute;
    }

    public function editAction()
    {
        return 'EditAction';
    }

    public function getService()
    {
        return $this->service;
    }
}
