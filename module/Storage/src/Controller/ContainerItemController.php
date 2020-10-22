<?php

namespace Storage\Controller;

use Core\Service\AccessValidateService;
use Core\Traits\FormatNumbers;
use Core\Traits\RestMethods;
use Core\Traits\RouteParams;
use JsonException;
use LogicException;
use Storage\Entity\ContainerItem;
use Storage\Form\ContainerItemForm;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\ContainerItemService;
use Storage\Service\ShipmentService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class ContainerItemController
 * @package Storage\Controller
 * @method CurrentDepartment currentDepartment()
 * @method bool hasAccess(string $className, string $permission)
 */
class ContainerItemController extends AbstractActionController
{
    use RouteParams;
    use FormatNumbers;
    use RestMethods;

    private int $depth = 80;
    private string $shipmentRoute = 'shipment';
    private ContainerItemService $service;
    private ContainerItemForm $form;
    private AccessValidateService $accessValidateService;

    /**
     * ContainerItemController constructor.
     * @param ContainerItemService $service
     * @param ContainerItemForm $form
     * @param $accessValidateService
     */
    public function __construct($service, $form, $accessValidateService)
    {
        $this->service = $service;
        $this->form = $form;
        $this->accessValidateService = $accessValidateService;
    }

    public function addAction()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $isFerrous = $currentDepartment->isBlack();
        $showPrice = $this->hasAccess(self::class, 'delete') || $this->hasAccess(self::class, 'edit');
        $this->form->addElements($currentDepartment->isBlack(), $showPrice);
        $this->form->bind(new ContainerItem());

        $view = new ViewModel([
            'form' => $this->form,
            'isFerrous' => $isFerrous,
        ]);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * @return Response|ViewModel
     */
    public function editAction()
    {
        $id = $this->getRouteId();
        $containerItem = $this->service->find($id);

        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $showPrice = $this->hasAccess(self::class, 'delete') || $this->hasAccess(self::class, 'edit');
        $this->form->addElements($currentDepartment->isBlack(), $showPrice);
        $this->form->bind($containerItem);

        if ($this->getRequest()->isPost()) {
            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $containerItem->getContainer()->getShipment()->getDate());
                $this->form->setInputFilter($containerItem->getInputFilter());
                $this->form->setData($this->getRequest()->getPost());
                if ($this->form->isValid()) {
                    $this->service->save($containerItem);
                    return $this->responseSuccess();
                }
                throw new LogicException('Некорректные данные.');
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }
        }

        $view = new ViewModel([
            'title' => 'Редактировать позицию',
            'form' => $this->form,
            'dep' => $currentDepartment->getId()
        ]);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * @return Response
     */
    public function deleteAction(): Response
    {
        $id = $this->getRouteId();
        $this->service->remove($id);
        return $this->redirect()->toRoute($this->shipmentRoute, ['department' => $this->currentDepartment()->getId()]);
    }
}
