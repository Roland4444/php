<?php

namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Reference\Entity\Employee;
use Reference\Form\EmployeeForm;
use Reference\Service\EmployeeService;

/**
 * Class EmployeeController
 * @package Reference\Controller
 */
class EmployeeController extends PlainReferenceController
{
    use FilterableController;

    protected $routeIndex = 'employee';

    /**
     * SpareController constructor.
     * @param EmployeeService $service
     * @param EmployeeForm $form
     */
    public function __construct(EmployeeService $service, EmployeeForm $form)
    {
        $this->service = $service;
        $this->form = $form;
        $this->entityInstance = new Employee();
    }
}
