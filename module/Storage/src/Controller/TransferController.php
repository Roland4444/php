<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Exception\ServiceException;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Helper\HasAccess;
use Core\Controller\CrudController;
use Application\Form\Filter\FromToElement;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\SubmitElement;
use Core\Service\DateService;
use Doctrine\ORM\ORMException;
use Reference\Entity\Metal;
use Storage\Entity\Transfer;
use Storage\Form\TransferForm;
use Storage\Plugin\CurrentDepartment;
use Storage\Presenter\TransferPresenter;
use Storage\Service\TransferService;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Class TransferController
 * @package Storage\Controller
 * @method HasAccess hasAccess() hasAccess(string $className, string $permission)
 * @method CurrentDepartment currentDepartment()
 * @method CurrentUser currentUser()
 */
class TransferController extends CrudController
{
    use FilterableController;

    protected string $indexRoute = 'transfer';

    /**
     * @var TransferService
     */
    protected $service;

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
     * Add action
     * @return Response|ViewModel
     */
    public function addAction()
    {
        $form = $this->getCreateForm();

        $message = '';
        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $data = $this->checkDataForCreate($postData->toArray());

                $entity = $this->getEntityForCreate($data);
                $form->bind($entity);

                $form->setData($data);

                if ($form->isValid()) {
                    if ($this->service->save($entity)) {
                        return $this->redirect()->toRoute($this->indexRoute, $this->indexRouteParams);
                    }
                    throw new ServiceException('Не удалось сохранить запись.');
                } else {
                    if (! empty($form->getMessages())) {
                        foreach ($form->getMessages() as $key => $msgs) {
                            foreach ($msgs as $msg) {
                                $message .= $key . ' - '. $msg;
                            }
                        }
                    }
                }
            } catch (ServiceException | \Exception $e) {
                $this->logger->err($e->getTraceAsString());
                $message = $e->getMessage();
            }
        }
        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'params' => $this->getIndexViewParams(),
            'form' => $form,
            'message' => $message ?? null,
            'sourceIsBlack' => false
        ]);
    }

    /**
     * Edit action
     * @return Response|ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        $id = $this->getRouteId();
        /** @var Transfer $entity */
        $entity = $this->getEntityForEdit($id);

        if (empty($entity) || ! $this->checkAccessToEdit($entity)) {
            $this->flashMessenger()->addMessage('Доступ запрещен.');
            return $this->redirect()->toRoute($this->indexRoute, $this->indexRouteParams);
        }

        $form = $this->getEditForm();
        $form->bind($entity);
        $message = '';
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
                $postData['metal'] = $entity->getMetal()->getId();
                $postData['source'] = $entity->getSource()->getId();
                $postData['dest'] = $entity->getDest()->getId();
                $postData['nakl1'] = $entity->getNakl1();
                if ($entity->getSource()->isBlack()) {
                    $postData['actual'] = 0;
                }
            }
            try {
                $this->checkDataForEdit($postData->toArray());

                $form->setInputFilter($entity->getInputFilter());
                $form->setData($postData);

                if ($form->isValid()) {
                    if ($this->service->save($entity, $this->getRequest())) {
                        return $this->redirect()->toRoute($this->indexRoute, $this->indexRouteParams);
                    }
                } else {
                    if (! empty($form->getMessages())) {
                        foreach ($form->getMessages() as $key => $msgs) {
                            foreach ($msgs as $msg) {
                                $message .= $key . ' - '. $msg;
                            }
                        }
                    }
                }
            } catch (ServiceException $e) {
                $message = $e->getMessage();
            }
        }

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'params' => $this->getIndexViewParams(),
            'id' => $id,
            'form' => $form,
            'message' => $message ?? null,
            'sourceIsBlack' => $entity->getSource()->isBlack()
        ]);
    }

    /**
     * Получить данные для индексной страницы
     * @param $params
     * @return mixed
     */
    public function getTableListData($params)
    {
        try {
            $departmentId = $this->currentDepartment()->getId();

            if (! in_array($params['department'], [0, $departmentId]) && $this->currentUser()->getDepartment() === null) {
                return $this->redirect()->toRoute($this->indexRoute, ['department' => $params['department']]);
            }

            $params['department'] = $departmentId;

            $itemList = $this->service->getTransferList($params);

            $avgSor = $this->service->getAvgSor($params);

            $presenter = new TransferPresenter($itemList, $avgSor);
            return $presenter->getFormatData();
        } catch (ORMException $e) {
            $this->logger->err($e->getTraceAsString());
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(self::class, 'add'),
            'delete' => $this->hasAccess(self::class, 'delete'),
            'edit' => $this->hasAccess(self::class, 'edit'),
            'showAvgSor' => $this->currentDepartment()->getDepartment()->isBlack()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getIndexViewParams()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new \LogicException('Current department can\'t be null');
        }
        return [
            'allowedDatesForEdit' => [date('Y-m-d'), date("Y-m-d", time() - 3600 * 24)],
            'currentDepartmentIsFerrous' => $currentDepartment->isBlack(),
            'currentDepartmentId' => $currentDepartment->getId()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterForm()
    {
        if ($this->currentUser()->getDepartment()) {
            return new SubmitElement(new FromToElement(new DateElement($this->entityManager)));
        }
        return new SubmitElement(new DepartmentElement(new FromToElement(new DateElement($this->entityManager))));
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreateForm()
    {
        $entity = new Transfer();
        $entity->setDate(new \DateTime());
        $metal = $this->entityManager->getRepository(Metal::class)->findOneByDef(true);
        $entity->setMetal($metal);
        $entity->setWeight(0);
        $entity->setActual(0);

        $form = new TransferForm($this->entityManager, $this->currentDepartment()->getId());
        $form->bind($entity);

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkAccessToEdit($entity)
    {
        if ($this->hasAccess(self::class, 'edit') && ! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
            if ($entity->getDest()->getId() !== $this->currentDepartment()->getId()) {
                return false;
            }
            $dateService = new DateService();
            if (! $dateService->checkDateInRangeDays($entity->getDate(), 7)) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditForm()
    {
        return new TransferForm($this->entityManager, null);
    }

    /**
     * @inheritdoc
     * @throws ServiceException
     * @throws \Exception
     */
    protected function checkDataForCreate(array $data)
    {
        if (empty($data['nakl1']) && empty($data["weight"])) {
            throw new ServiceException('Номер накладной отправителя не должен быть пустой.');
        }
        $dateService = new DateService();
        if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) && $this->hasAccess(self::class, 'edit')) {
            if (! $dateService->checkDateInRangeDays(new \DateTime($data['date']), 7)) {
                throw new ServiceException('Нельзя создать запись с такой датой.');
            }
        }
        if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) && ! $this->hasAccess(self::class, 'edit')) {
            if (! $dateService->checkDateInRangeTwoDays(new \DateTime($data['date']))) {
                throw new ServiceException('Нельзя создать запись с такой датой.');
            }
        }
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new \LogicException('Current department can\'t be null');
        }
        if ($currentDepartment->isBlack() && ! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
            $data['weight'] = 0;
        }

        $data['actual'] = 0;
        return $data;
    }

    /**
     * @param $data
     * @return array
     * @throws ServiceException
     * @throws \Exception
     */
    protected function checkDataForEdit(array $data)
    {
        if (empty($data['nakl1']) && empty($data["weight"])) {
            throw new ServiceException("Номер накладной отправителя не должен быть пустой.");
        }
        $dateService = new DateService();
        if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) && $this->hasAccess(self::class, 'edit')) {
            if (! $dateService->checkDateInRangeMonth(new \DateTime($data['date']))) {
                throw new ServiceException('Нельзя создать запись с такой датой.');
            }
        }
        if (! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) && ! $this->hasAccess(self::class, 'edit')) {
            if (! $dateService->checkDateInRangeTwoDays(new \DateTime($data['date']))) {
                throw new ServiceException('Нельзя создать запись с такой датой.');
            }
            $data['actual'] = 0;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityForCreate(array $data)
    {
        $entity = new Transfer();
        $entity->setSource($this->currentDepartment()->getDepartment());
        return $entity;
    }
}
