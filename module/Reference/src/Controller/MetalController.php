<?php
namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Core\Traits\RestMethods;
use Reference\Form\MetalForm;
use Reference\Service\MetalService;

/**
 * Class MetalController
 * @package Reference\Controller
 */
class MetalController extends AbstractReferenceController
{
    use FilterableController, RestMethods;
    /**
     * MetalController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, MetalService::class, MetalForm::class);
        $this->routeIndex = "metal";
    }

    public function listAction()
    {
        $data = $this->service->getListForJson();
        return $this->responseSuccess(['data' => $data]);
    }
}
