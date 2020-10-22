<?php

namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Core\Traits\RestMethods;
use Reference\Entity\Department;
use Reference\Form\DepartmentForm;
use Reference\Service\DepartmentService;

/**
 * Class DepartmentController
 * @package Reference\Controller
 */
class DepartmentController extends PlainReferenceController
{
    use FilterableController, RestMethods;

    public $routeIndex = "department";

    /**
     * SpareController constructor.
     * @param DepartmentService $service
     * @param DepartmentForm $form
     */
    public function __construct(DepartmentService $service, DepartmentForm $form)
    {
        $this->service = $service;
        $this->form = $form;
        $this->entityInstance = new Department();
    }

    public function listAction()
    {
        $data = $this->service->getListForJson();
        return $this->responseSuccess(['data' => $data]);
    }
}
