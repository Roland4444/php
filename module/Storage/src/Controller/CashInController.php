<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Application\Helper\HasAccess;
use Core\Controller\CrudController;
use Doctrine\ORM\ORMException;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Finance\Entity\MoneyToDepartment;
use Finance\Service\BankService;
use Finance\Service\MoneyToDepartmentService;
use Reference\Entity\Department;
use Storage\Plugin\CurrentDepartment;
use Storage\Presenter\CashInPresenter;
use Zend\Mvc\MvcEvent;

/**
 * Class CashInController
 * @package Storage\Controller
 * @method HasAccess hasAccess() hasAccess(string $className, string $permission)
 * @method CurrentDepartment currentDepartment()
 * @method CurrentUser currentUser()
 */
class CashInController extends CrudController
{
    use FilterableController;
    protected string $indexRoute = 'storageCashIn';

    /**
     * @var MoneyToDepartmentService
     */
    protected $service;

    /**
     * {@inheritdoc}
     */
    protected function getCurrentDepartmentId(): int
    {
        return $this->currentDepartment()->getId();
    }

    protected function getCurrentDepartment(): Department
    {
        return $this->currentDepartment()->getDepartment();
    }

    /**
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->indexRouteParams = [
            'department' => $this->getCurrentDepartmentId()
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
            'currentDepartmentId' => $this->getCurrentDepartmentId()
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
        ];
    }

    /**
     * Получить данные для индексной страницы
     * @param $params
     * @return mixed
     */
    protected function getTableListData($params)
    {
        try {
            if ($params['department'] != 0 && $this->params()->fromRoute('department') != $params['department'] && $this->currentUser()->getDepartment() === null) {
                return $this->redirect()->toRoute($this->indexRoute, ['department' => $params['department']]);
            }

            $itemList = $this->service->findByParams($params['startdate'], $params['enddate'], $this->getCurrentDepartmentId());

            $presenter = new CashInPresenter($itemList);
            return $presenter->getFormatData();
        } catch (ORMException $e) {
            $this->logger->err($e->getTraceAsString());
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterForm()
    {
        if ($this->currentUser()->getDepartment()) {
            return new SubmitElement(new DateElement($this->entityManager));
        }

        return new SubmitElement(new DepartmentElement(new DateElement($this->entityManager)));
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityForCreate(array $data)
    {
        $entity = new MoneyToDepartment();
        $entity->setDate(date('Y-m-d'));
        $entity->setDepartment($this->getCurrentDepartment());
        $entity->setVerified(false);
        $entity->setBank($this->services[BankService::class]->findCash());
        return $entity;
    }

    /**
     * Create form
     * @return mixed
     */
    protected function getCreateForm()
    {
        $form = new \Storage\Form\CashIn();
        $form->setHydrator(new DoctrineObject($this->entityManager))->setObject(new MoneyToDepartment);
        $form->setValidationGroup('money');
        return $form;
    }

    /**
     * Edit form
     * @return mixed
     */
    protected function getEditForm()
    {
        $form = new \Storage\Form\CashIn();
        $form->setHydrator(new DoctrineObject($this->entityManager));
        $form->setValidationGroup('money');
        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function checkAccessToEdit($entity)
    {
        $date = date_create($entity->getDate());
        return $this->services['dateService']->checkDateInRangeDays($date, 3);
    }
}
