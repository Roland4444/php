<?php
namespace Modules\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\CustomerTextElement;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\SubmitElement;
use Application\Form\Filter\VehicleElement;
use Application\Form\Filter\WeightElement;
use Modules\Form\MoveVehiclesEditForm;

/**
 * Class CompletedVehicleTripsController
 * @package Modules\Controller
 * @method CurrentUser currentUser()
 */
class CompletedVehicleTripsController extends MoveVehiclesController
{
    protected string $indexRoute = 'completedVehicleTrips';

    protected function getEditForm()
    {
        return new MoveVehiclesEditForm($this->entityManager);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        $entityManager = $this->entityManager;

        return new SubmitElement(
            new WeightElement(
                new VehicleElement(
                    new DepartmentElement(new CustomerTextElement(new DateElement($entityManager)), $this->currentUser()->isAdmin())
                )
            )
        );
    }
}
