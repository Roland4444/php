<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\IElement;
use Core\Service\AccessValidateService;
use Core\Traits\RestMethods;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use JsonException;
use LogicException;
use Reference\Service\ShipmentTariffService;
use Reference\Service\TraderService;
use Storage\Entity\Shipment;
use Storage\Form\ShipmentForm;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\ShipmentService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Validator\Exception\InvalidArgumentException;
use Zend\View\Model\ViewModel;
use Application\Form\Filter\TraderElement;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\SubmitElement;

/**
 * Class ShipmentController
 * @package Storage\Controller
 * @method CurrentDepartment currentDepartment()
 * @method CurrentUser currentUser()
 * @method bool hasAccess(string $className, string $permission)
 * @method FlashMessenger flashMessenger()
 */
class ShipmentController extends AbstractActionController
{
    use FilterableController;
    use RestMethods;
    use RouteParams;

    private int $depth = 80;
    private EntityManager $entityManager;
    private AccessValidateService $accessValidateService;
    private ShipmentService $shipmentService;
    private TraderService $traderService;
    private ShipmentTariffService $tariffService;
    private ShipmentForm $form;

    private string $indexRoute = 'shipment';

    public function __construct($entityManager, $shipmentService, $accessValidateService, $traderService, $tariffService, $form)
    {
        $this->entityManager = $entityManager;
        $this->shipmentService = $shipmentService;
        $this->accessValidateService = $accessValidateService;
        $this->traderService = $traderService;
        $this->tariffService = $tariffService;
        $this->form = $form;
    }

    public function indexAction()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        if ($this->currentDepartment()->isHide()) {
            throw new LogicException('Department not found');
        }

        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        $departmentFromRoute = $this->params()->fromRoute('department');
        $userDoesNotHasDepartment = $this->currentUser()->getDepartment() === null;
        if ($params['department'] != 0 && $departmentFromRoute != $params['department'] && $userDoesNotHasDepartment) {
            return $this->redirect()->toRoute($this->indexRoute, ['department' => $params['department']]);
        }

        $rows = $this->shipmentService->findByParams($params['startdate'], $params['enddate'], $currentDepartment->getId(), $params['trader']);
        $total = $this->shipmentService->getTotal($rows);
        $itemTotal = $this->shipmentService->getTotalByMetal($rows);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'route' => $this->indexRoute,
            'dep' => $currentDepartment->getId(),
            'departmentName' => $currentDepartment->getName(),
            'rows' => $rows,
            'form' => $filterForm->getForm($params),
            'total' => $total,
            'itemTotal' => $itemTotal,
        ]);
    }

    public function addAction(): ViewModel
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }

        $shipmentId = $this->getRouteId();
        $containerId = $this->params()->fromRoute('container', 0);

        $traders = $this->traderService->getItemsForSelect();
        $tariffs = $this->tariffService->getItemsForSelect();

        return new ViewModel([
            'route' => $this->indexRoute,
            'isFerrous' => $currentDepartment->isBlack(),
            'dep' => $currentDepartment->getId(),
            'shipment_id' => $shipmentId,
            'container_id' => $containerId,
            'traders' => json_encode($traders),
            'tariffs' => json_encode($tariffs),
            'permissions' => $this->getPermissions()
        ]);
    }

    public function addShipmentAction(): ViewModel
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $showRate = $this->hasAccess(self::class, 'delete');
        $this->form->addElements($currentDepartment->getType(), $showRate);

        $shipmentId = $this->params()->fromQuery('id');
        if ($shipmentId) {
            $shipment = $this->shipmentService->find($shipmentId);
        } else {
            $shipment = new Shipment();
            $shipment->setDepartment($currentDepartment);
            $shipment->setTrader($this->traderService->findDefault());
            $shipment->setTariff($this->tariffService->findDefault());
        }
        $this->form->bind($shipment);

        $view = new ViewModel([
            'shipmentForm' => $this->form,
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function saveAjaxAction(): Response
    {
        try {
            if (! $this->getRequest()->isPost()) {
                throw new LogicException('Method Not Allowed');
            }
            $currentDepartment = $this->currentDepartment()->getDepartment();
            if ($currentDepartment === null) {
                throw new LogicException('Current department can\'t be null');
            }
            $postData = $this->getRequest()->getPost();
            $this->accessValidateService->validateAccessForDays($this->depth, $postData['date']);

            $shipment = new Shipment();
            $this->form->setInputFilter($shipment->getInputFilter());
            $this->form->setData($postData);
            if (! $this->form->isValid()) {
                throw new InvalidArgumentException('Некорректные данные формы shipment.');
            }
            $shipment = $this->shipmentService->parseShipment($postData, $currentDepartment);
            $this->shipmentService->save($shipment, $this->getRequest());
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function editAction()
    {
        $id = $this->getRouteId();
        /** @var Shipment $row */
        $row = $this->shipmentService->find($id);

        $showRate = $this->hasAccess(self::class, 'delete');
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $this->form->addElements($currentDepartment->getType(), $showRate);
        $this->form->bind($row);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            try {
                $this->accessValidateService->validateAccessForDays($this->depth, $postData['date'], $row);
            } catch (\Exception $e) {
                $this->flashMessenger()->addMessage($e->getMessage());
                return $this->redirectToIndex();
            }
            /** @var Shipment $duplicate */
            $duplicate = $this->shipmentService->getDuplicate($postData, $currentDepartment->getId());
            if ($duplicate && $duplicate->getId() !== $row->getId()) {
                $duplicate->addContainers($row->getContainers());
                $this->shipmentService->save($duplicate);
                $row->removeContainers();
                $this->shipmentService->remove($row->getId());
                return $this->redirectToIndex();
            }

            $this->form->setInputFilter($row->getInputFilter())->setData($postData);
            if (! $this->form->isValid()) {
                $this->flashMessenger()->addMessage('Введенные данные не корректны');
                return $this->redirectToIndex();
            }
            $this->shipmentService->save($row, $this->getRequest());
            return $this->redirectToIndex();
        }

        return new ViewModel([
            'route' => $this->indexRoute,
            'title' => 'Редактировать отгрузку металла',
            'form' => $this->form,
            'dep' => $currentDepartment->getId(),
            'id' => $id
        ]);
    }

    private function redirectToIndex(): Response
    {
        return $this->redirect()->toRoute($this->indexRoute, ['department' => $this->currentDepartment()->getId()]);
    }

    public function deleteAction(): Response
    {
        $id = $this->getRouteId();
        $this->shipmentService->remove($id);
        return $this->redirect()->toRoute($this->indexRoute, ['department' => $this->currentDepartment()->getId()]);
    }

    /**
     * @return Response
     * @throws JsonException
     */
    public function getItemAction()
    {
        $id = $this->getRouteId();
        $item = $this->shipmentService->findByItemId($id);

        if (! $item) {
            return $this->responseError('Запись не найдена');
        }
        $summary = $this->shipmentService->getSummaryDataByItem($item, $this->currentDepartment()->getId());
        return $this->responseSuccess($summary);
    }

    protected function getFilterForm(): IElement
    {
        if ($this->currentUser()->getDepartment()) {
            return new SubmitElement(new TraderElement(new DateElement($this->entityManager)));
        }
        return new SubmitElement(new DepartmentElement(new TraderElement(new DateElement($this->entityManager))));
    }

    protected function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'money' => $this->hasAccess(static::class, 'money'),
        ];
    }
}
