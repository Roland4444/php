<?php
namespace Reference\Controller;

use Reference\Entity\MetalGroup;
use Reference\Form\MetalGroupForm;
use Reference\Service\MetalGroupService;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class MetalGroupController extends PlainReferenceController
{
    protected $routeIndex = 'metalGroup';
    protected $service;
    protected $form;

    /**
     * CategoryController constructor.
     * @param MetalGroupService $service
     * @param MetalGroupForm $form
     */
    public function __construct(MetalGroupService $service, MetalGroupForm $form)
    {
        $this->service = $service;
        $this->form = $form;
        $this->entityInstance = new MetalGroup();
    }
}
