<?php
namespace Reference\Controller;

use Reference\Entity\ContainerOwner;
use Reference\Form\ContainerOwnerForm;
use Reference\Service\ContainerOwnerService;

/**
 * Class ContainerOwnerController
 * @package Reference\Controller
 */
class ContainerOwnerController extends PlainReferenceController
{
    protected $routeIndex = 'containerOwner';

    /**
     * ContainerOwnerController constructor.
     * @param ContainerOwnerService $service
     * @param ContainerOwnerForm $form
     */
    public function __construct(ContainerOwnerService $service, ContainerOwnerForm $form)
    {
        $this->service = $service;
        $this->form = $form;
        $this->entityInstance = new ContainerOwner();
    }
}
