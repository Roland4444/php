<?php

namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Core\Utils\Options;
use Reference\Form\WarehouseForm;
use Reference\Service\WarehouseService;

/**
 * Class WarehouseController
 * @package Reference\Controller
 */
class WarehouseController extends AbstractReferenceController
{
    use FilterableController;

    /**
     * WarehouseController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, WarehouseService::class, WarehouseForm::class);
        $this->routeIndex = "warehouse";
    }

    /**
     * @throws
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $warehouse = $this->service->find($id);
        $warehouse->setOption(Options::CLOSED, true);
        $this->service->save($warehouse);
        $this->flashMessenger()->addMessage('Хоз. склад закрыт');
        return $this->redirect()->toRoute($this->routeIndex);
    }
}
