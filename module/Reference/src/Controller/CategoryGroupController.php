<?php
namespace Reference\Controller;

use Reference\Entity\CategoryGroup;
use Reference\Form\CategoryGroupForm;
use Reference\Service\CategoryGroupService;

/**
 * Class CategoryGroupController
 * @package Reference\Controller
 */
class CategoryGroupController extends PlainReferenceController
{
    protected $routeIndex = 'categoryGroup';

    /**
     * CategoryController constructor.
     * @param CategoryGroupService $service
     * @param CategoryGroupForm $form
     */
    public function __construct(CategoryGroupService $service, CategoryGroupForm $form)
    {
        $this->service = $service;
        $this->form = $form;
        $this->entityInstance = new CategoryGroup();
    }
}
