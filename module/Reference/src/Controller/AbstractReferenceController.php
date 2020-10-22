<?php

namespace Reference\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use phpDocumentor\Reflection\Types\Array_;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service\BaseService;

/**
 * Class ReferenceController
 * @package Reference\Controller
 * @method  \Zend\Mvc\Plugin\FlashMessenger\FlashMessenger flashMessenger()
 */
abstract class AbstractReferenceController extends AbstractActionController
{
    /**
     * Роут со списком сущностей
     * @var
     */
    protected $routeIndex;

    protected $service;

    /**
     * @var array Для проверки уникального значения
     */
    protected $propertyForExistValidate = ['name'];

    protected $entityManager;

    private $formClass;

    /**
     * AbstractReferenceController constructor.
     * @param $container
     * @param $serviceClass
     * @param $formClass
     */
    public function __construct($container, $serviceClass, $formClass)
    {
        $this->service = $container->get($serviceClass);
        $this->entityManager = $container->get('entityManager');
        $this->formClass = $formClass;
    }

    protected function getService()
    {
        return $this->service;
    }

    protected function getForm()
    {
        $form = new $this->formClass($this->entityManager);
        $entityName = $this->service->getEntity();
        $form->setHydrator(new DoctrineObject($this->entityManager))->setObject(new $entityName);
        $form->addElements();
        return $form;
    }

    /**
     * Список сущностей
     * @return ViewModel
     */
    public function indexAction()
    {
        $form = $this->filterForm($this->getRequest(), $this->routeIndex);

        $params = $form ? $form->getFilterParams($this->routeIndex) : null;
        $rows = $this->findRowsForIndex($params);

        return new ViewModel([
            'routeIndex' => $this->routeIndex,
            'rows' => $rows,
            'form' => $form ? $form->getForm($params) : null,
            'permissions' => $this->getPermissions()
        ]);
    }

    private function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }

    /**
     * Find rows for index page
     *
     * @param array $params
     *
     * @return mixed
     */
    protected function findRowsForIndex(?array $params)
    {
        return $this->getService()->findAll();
    }

    /**
     * Возвращает форму фильтра
     */
    protected function getFilterForm()
    {
    }

    /**
     * Prepare entity for bind to form
     * @param $entityName
     * @return mixed
     */
    protected function createEntityForBind($entityName)
    {
        return new $entityName;
    }

    /**
     * Добавить сущность
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAction()
    {
        $entityName = $this->service->getEntity();
        $form = $this->getForm();
        $form->bind($this->createEntityForBind($entityName));
        $error = '';

        if ($this->getRequest()->isPost()) {
            $entityName = $this->service->getEntity();
            $repository = $this->entityManager->getRepository($entityName);
            $form->addNoObjectExistsValidators($this->propertyForExistValidate, $this->getRequest()->getPost(), $repository);

            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                try {
                    $this->service->save($form->getObject(), $this->getRequest());
                    return $this->redirect()->toRoute($this->routeIndex);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            } else {
                $error = 'Введенные данные некорректны';
            }
        }

        return new ViewModel([
            'form' => $form,
            'routeIndex' => $this->routeIndex,
            'action' => 'add',
            'error' => $error,
            'id' => null
        ]);
    }

    /**
     * Редактировать сущность
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');

        $entity = $this->service->find($id);
        $form = $this->getForm();
        $form->bind($entity);

        $this->prepareDataForEdit($form, $entity);

        $error = '';
        if ($this->getRequest()->isPost()) {
            $entityName = $this->service->getEntity();
            $repository = $this->entityManager->getRepository($entityName);
            $form->addNoObjectExistsValidators($this->propertyForExistValidate, $this->getRequest()->getPost(), $repository, $entity);

            $form->setData($this->getRequest()->getPost());
            try {
                if ($form->isValid()) {
                    $this->service->save($form->getData(), $this->getRequest());
                    return $this->redirect()->toRoute($this->routeIndex);
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return new ViewModel([
            'form' => $form,
            'routeIndex' => $this->routeIndex,
            'action' => 'edit',
            'id' => $id,
            'error' => $error,
        ]);
    }

    /**
     * Подготавливает параметры для редактирования
     *
     * @param $form
     * @param $entity
     */
    protected function prepareDataForEdit($form, $entity)
    {
    }

    /**
     * Удаление сущности
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');

        $this->service->remove($id);

        $this->redirect()->toRoute($this->routeIndex);
    }
}
