<?php

namespace Reference\Controller;

use Reference\Entity\Category;
use Reference\Form\CategoryForm;
use Reference\Service\CategoryService;

/**
 * Class CategoryController
 * @package Reference\Controller
 */
class CategoryController extends PlainReferenceController
{
    protected $routeIndex = 'category';

    /**
     * CategoryController constructor.
     * @param CategoryService $service
     * @param CategoryForm $form
     */
    public function __construct(CategoryService $service, CategoryForm $form)
    {
        $this->service = $service;
        $this->form = $form;
        $this->entityInstance = new Category();
    }
}
