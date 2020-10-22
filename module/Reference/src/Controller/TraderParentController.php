<?php
namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Reference\Form\TraderParentForm;
use Reference\Service\TraderParentService;

class TraderParentController extends AbstractReferenceController
{
    use FilterableController;

    /**
     * TraderParentController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, TraderParentService::class, TraderParentForm::class);
        $this->routeIndex = "reference\\traderParent";
    }
}
