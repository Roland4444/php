<?php

namespace Storage\Controller;

use Core\Service\AccessValidateService;
use Core\Traits\RestMethods;
use Core\Traits\RouteParams;
use LogicException;
use Reference\Service\ContainerOwnerService;
use Storage\Entity\Container;
use Storage\Form\ContainerForm;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\ContainerService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class ContainerController
 * @package Storage\Controller
 * @method CurrentDepartment currentDepartment()
 */
class ContainerController extends AbstractActionController
{
    use RestMethods;
    use RouteParams;

    private int $depth = 80;
    private ContainerService $containerService;
    private ContainerOwnerService $containerOwnerService;
    private AccessValidateService $accessValidateService;
    private ContainerForm $form;

    public function __construct($containerService, $containerOwnerService, $accessValidateService, $form)
    {
        $this->containerService = $containerService;
        $this->containerOwnerService = $containerOwnerService;
        $this->accessValidateService = $accessValidateService;
        $this->form = $form;
    }

    public function addAction(): ViewModel
    {
        $containerId = $this->params()->fromQuery('container');

        if ($containerId) {
            $container = $this->containerService->find($containerId);
        } else {
            $container = new Container();
        }

        $this->form->bind($container);

        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $isFerrous = $currentDepartment->isBlack();

        $view = new ViewModel([
            'route' => 'storage_container',
            'containerForm' => $this->form,
            'formElements' => $isFerrous ? ['name', 'owner'] : ['name', 'owner', 'submit'],
            'action' => 'add',
            'id' => null,
            'dep' => null
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function editAction()
    {
        $id = $this->getRouteId();

        $row = $this->containerService->find($id);

        $this->form->bind($row);

        $departmentId = $this->currentDepartment()->getId();

        try {
            $this->accessValidateService->validateAccessForDays($this->depth, $row->getShipment()->getDate());
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('shipment', ['department' => $departmentId]);
        }

        if ($this->getRequest()->isPost()) {
            $this->form->setInputFilter($row->getInputFilter());
            $this->form->setData($this->getRequest()->getPost());
            if ($this->form->isValid()) {
                $ownerId = $this->getRequest()->getPost('owner');
                $owner = $this->containerOwnerService->find($ownerId);
                $row->getExtraOwner()->setOwner($owner);
                $this->containerService->save($row);
                return $this->redirect()->toRoute('shipment', ['department' => $departmentId]);
            }
        }

        return new ViewModel([
            'title' => 'Редактировать контейнер',
            'route' => 'storage_container',
            'containerForm' => $this->form,
            'dep' => $departmentId,
            'action' => 'edit',
            'id' => $id,
            'formElements' => ['name', 'owner', 'submit'],
        ]);
    }

    public function deleteAction(): Response
    {
        $id = $this->getRouteId();
        $this->containerService->remove($id);
        return $this->redirect()->toRoute('shipment', ['department' => $this->currentDepartment()->getId()]);
    }

    public function getColorDepartmentsContainersByDateAction(): Response
    {
        $date = $this->params()->fromRoute('date');

        return $this->responseSuccess([
            'message' => 'list of containers',
            'data' => $this->containerService->getColorDepartmentsContainersByDate($date)
        ]);
    }
}
