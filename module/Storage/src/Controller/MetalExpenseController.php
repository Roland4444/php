<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Exception\ServiceException;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\PaymentElement;
use Application\Form\Filter\SubmitElement;
use Application\Form\Filter\CustomerElement;
use Application\Helper\HasAccess;
use Core\Controller\CrudController;
use Core\Traits\RestMethods;
use Storage\Entity\MetalExpense;
use Storage\Entity\PurchaseDeal;
use Storage\Form\MetalExpenseForm;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\MetalExpenseService;
use Storage\Service\PurchaseDealService;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Reference\Service\CustomerService;

/**
 * Class MetalExpenseController
 * @package Storage\Controller
 * @method HasAccess hasAccess() hasAccess(string $className, string $permission)
 * @method CurrentDepartment currentDepartment()
 * @method CurrentUser currentUser()
 */
class MetalExpenseController extends CrudController
{
    use RestMethods;
    use FilterableController;
    protected string $indexRoute = 'storageMetalExpense';

    /**
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->indexRouteParams = [
            'department' => $this->currentDepartment()->getId()
        ];
        return parent::onDispatch($e);
    }

    /**
     * {@inheritdoc}
     */
    protected function getIndexViewParams()
    {
        return [
            'route' => $this->indexRoute,
            'currentDepartmentId' => $this->currentDepartment()->getId(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }

    public function indexAction()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new \LogicException('Current department can\'t be null');
        }
        if ($this->currentDepartment()->isHide()) {
            die('');
        }

        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        if ($params['department'] != 0 && $this->params()->fromRoute('department') != $params['department'] && $this->currentUser()->getDepartment() === null) {
            return $this->redirect()->toRoute($this->indexRoute, ['department' => $params['department']]);
        }

        $params['department'] = $currentDepartment->getId();

        $rows = $this->service->findAll($params);
        $sum = $this->service->getSum($params);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'params' => $this->getIndexViewParams(),
            'rows' => $rows,
            'sum' => $sum,
            'form' => $filterForm->getForm($params),
        ]);
    }

    public function dealAction()
    {
        $dealId = $this->getRouteId();

        /** @var PurchaseDeal $deal */
        $deal = $this->services[PurchaseDealService::class]->find($dealId);
        $paid = $this->services[MetalExpenseService::class]->getSumByDeal($dealId);

        $debts = $deal->getSum() - $paid;

        $departmentId = $this->currentDepartment()->getId();
        $form = new MetalExpenseForm($this->entityManager, $departmentId, []);
        $form->setValidationGroup('money');

        if ($this->getRequest()->isPost()) {
            try {
                $form->setData($this->getRequest()->getPost());
                if ($form->isValid()) {
                    $payment = $this->getRequest()->getPost('money');
                    $isDiamond = $this->getRequest()->getPost('diamond');

                    if ($payment > $debts) {
                        throw new ServiceException('Выплата не может быть больше задолжености');
                    }

                    $this->services[MetalExpenseService::class]->payDeal($deal, $payment, $isDiamond);
                    return $this->redirect()->toRoute($this->indexRoute, ['action' => 'deal', 'id' => $dealId, 'department' => $departmentId]);
                }
            } catch (ServiceException $e) {
                $message = $e->getMessage();
            }
        }

        return new ViewModel([
            'deal' => $deal,
            'paid' => $paid,
            'form' => $form,
            'debts' => $debts,
            'backRoute' => 'purchase',
            'route' => 'storageMetalExpense',
            'departmentId' => $departmentId,
            'message' => $message ?? null,
            'permissions' => $this->getPermissions()
        ]);
    }

    public function payWeighingAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $this->service->payWeighing(
                    json_decode($this->getRequest()->getContent(), true)
                );
            } catch (\Exception $e) {
                return $this->responseError(['message' => $e->getMessage()], 500);
            }

            return $this->responseSuccess(['message' => 'Взвешивание оплачено']);
        }

        return $this->responseError(['message' => 'illegal action'], 405);
    }
    /**
     * {@inheritdoc}
     */
    protected function getCreateForm()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new \LogicException('Current department can\'t be null');
        }
        $form = new MetalExpenseForm($this->entityManager, $currentDepartment->getId(), []);
        $row = new MetalExpense();

        $row->setDepartment($currentDepartment);
        $row->setCustomer($this->services[CustomerService::class]->findDefault());
        if ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) {
            $row->setFormal(1);
        }
        $form->bind($row);
        return $form;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function checkDataForCreate(array $data)
    {
        $data['department'] = $this->currentDepartment()->getId();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityForCreate(array $data)
    {
        $entity = new MetalExpense();
        if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
            $entity->setFormal(0);
        }
        $entity->setDepartment($this->currentDepartment()->getDepartment());
        return $entity;
    }

    /**
     * Edit form
     * @return mixed
     */
    protected function getEditForm()
    {
        return new MetalExpenseForm($this->entityManager, $this->currentDepartment()->getId(), []);
    }

    /**
     * Получить данные для индексной страницы
     *
     * @param $params
     *
     * @return mixed
     */
    protected function getTableListData($params)
    {
        // TODO: Implement getTableListData() method.
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterForm()
    {
        if ($this->currentUser()->getDepartment()) {
            return new SubmitElement(new PaymentElement(new CustomerElement(new DateElement($this->entityManager))));
        }

        return new SubmitElement(new DepartmentElement(new PaymentElement(new CustomerElement(new DateElement($this->entityManager)))));
    }
}
