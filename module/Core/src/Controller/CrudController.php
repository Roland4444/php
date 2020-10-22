<?php

namespace Core\Controller;

use Application\Exception\ServiceException;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Storage\Plugin\CurrentDepartment;
use Zend\Http\Response;
use Zend\I18n\View\Helper\NumberFormat;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Logger;

/**
 * Class CrudController
 * @package Core\Controller
 * @method CurrentDepartment currentDepartment()
 */
abstract class CrudController extends AbstractActionController
{
    use RouteParams;

    protected string $indexRoute;
    protected $indexRouteParams = [];

    protected $service;
    protected Logger $logger;
    protected EntityManager $entityManager;
    protected array $services;

    /**
     * Получить данные для индексной страницы
     * @param $params
     * @return mixed
     * @throws ORMException
     */
    abstract protected function getTableListData($params);

    /**
     * Filter form
     */
    abstract protected function getFilterForm();

    /**
     * Create form
     * @return mixed
     */
    abstract protected function getCreateForm();

    /**
     * Edit form
     * @return mixed
     */
    abstract protected function getEditForm();

    /**
     * Get access to see components
     */
    abstract protected function getPermissions();

    /**
     * CrudController constructor.
     * @param $entityManager
     * @param $logger
     * @param $service
     * @param $services
     */
    public function __construct($entityManager, $logger, $service, $services)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->service = $service;
        $this->services = $services;

        if (! empty($services['dateService'])) {
            $this->dateService = $services['dateService'];
        }
    }

    /**
     * Index action
     * @return ViewModel
     * @throws ORMException
     */
    public function indexAction()
    {
        if ($this->currentDepartment()->isHide()) {
            die('');
        }

        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        $data = $this->getTableListData($params);

        return new ViewModel([
            'form' => $filterForm->getForm($params),
            'data' => $data,
            'permissions' => $this->getPermissions(),
            'params' => $this->getIndexViewParams()
        ]);
    }

    /**
     * Add action
     * @return Response|ViewModel
     */
    public function addAction()
    {
        $form = $this->getCreateForm();

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
                    } else {
                        throw new ServiceException('Не удалось сохранить запись.');
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
        ]);
    }

    /**
     * Edit action
     * @return Response|ViewModel
     */
    public function editAction()
    {
        $id = $this->getRouteId();
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

            try {
                $this->checkDataForEdit($postData->toArray());

                $form->setInputFilter($entity->getInputFilter());
                $form->setData($this->getRequest()->getPost());

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
        ]);
    }

    /**
     * Экшн удаления записи
     * @return Response
     */
    public function deleteAction()
    {
        if ($this->currentDepartment()->isHide()) {
            die('');
        }
        $id = $this->getRouteId();
        $this->service->remove($id);
        return $this->redirect()->toRoute($this->indexRoute, $this->indexRouteParams);
    }

    /**
     * @param $number
     * @return string
     */
    protected function formatNumber($number)
    {
        $numberFormat = new NumberFormat();
        return $numberFormat->__invoke(
            $number,
            \NumberFormatter::DECIMAL,
            \NumberFormatter::TYPE_DEFAULT,
            "ru_RU"
        );
    }


    /**
     * Custom params for view
     */
    protected function getIndexViewParams()
    {
    }

    /**
     * Check access to edit method
     * @param $entity
     * @return bool
     */
    protected function checkAccessToEdit($entity)
    {
        return true;
    }

    /**
     * @param array $data
     * @throws ServiceException
     */
    protected function checkDataForEdit(array $data)
    {
    }

    /**
     * @param array $data
     * @return array
     */
    protected function checkDataForCreate(array $data)
    {
        return $data;
    }

    /**
     * Получить entity для add action
     *
     * @param array $data
     */
    protected function getEntityForCreate(array $data)
    {
    }

    protected function getEntityForEdit($id)
    {
        return $this->service->find($id);
    }
}
